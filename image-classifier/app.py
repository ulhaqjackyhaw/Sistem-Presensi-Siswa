import os, time, threading, io
import numpy as np
import torch
from PIL import Image
from flask import Flask, request, jsonify
from flask_cors import CORS
from facenet_pytorch import MTCNN, InceptionResnetV1
from torchvision import transforms

app = Flask(__name__)
CORS(app)


STUDENT_PHOTOS_DIR = os.path.abspath(
    os.path.join(os.path.dirname(__file__), "../storage/app/public/student-photos")
)

# if not os.path.exists(STUDENT_PHOTOS_DIR):
#     app.logger.error("Directory %s does not exist.", STUDENT_PHOTOS_DIR)
# else:
#     app.logger.info("Directory %s exists.", STUDENT_PHOTOS_DIR)

# app.logger.info("STUDENT_PHOTOS_DIR resolved to: %s", STUDENT_PHOTOS_DIR)

device = torch.device("cuda" if torch.cuda.is_available() else "cpu")
mtcnn = MTCNN(image_size=160, margin=0, keep_all=False, device=device)
resnet = InceptionResnetV1(pretrained="vggface2").eval().to(device)

db_embeddings = {}
CACHE_LOCK = threading.Lock()
REFRESH_INTERVAL = 300

augmentations = transforms.Compose(
    [
        transforms.RandomHorizontalFlip(p=0.5),
        transforms.RandomRotation(degrees=10),
        transforms.ColorJitter(brightness=0.2, contrast=0.2, saturation=0.2),
        # bisa tambah Gaussian blur, noise, dsb
    ]
)


def load_db_embeddings():
    global db_embeddings
    temp = {}
    for fname in os.listdir(STUDENT_PHOTOS_DIR):
        if not fname.lower().endswith((".jpg", "jpeg", "png")):
            continue
        nisn = fname.split("_")[0]
        path = os.path.join(STUDENT_PHOTOS_DIR, fname)

        try:
            img = Image.open(path).convert("RGB")
            emb_list = []

            face = mtcnn(img)
            if face is not None:
                emb = resnet(face.unsqueeze(0).to(device)).cpu().detach().numpy()[0]
                emb_list.append(emb)

            for _ in range(4):
                aug_img = augmentations(img)
                face = mtcnn(aug_img)
                if face is not None:
                    emb_aug = (
                        resnet(face.unsqueeze(0).to(device)).cpu().detach().numpy()[0]
                    )
                    emb_list.append(emb_aug)

            if emb_list:
                mean_emb = np.mean(emb_list, axis=0)
                temp[nisn] = mean_emb / np.linalg.norm(mean_emb)

        except Exception as e:
            app.logger.warning(f"Gagal embed {fname}: {e}")

    with CACHE_LOCK:
        db_embeddings = temp
        app.logger.info(f"Refreshed db_embeddings: {len(db_embeddings)} siswa")


def refresher():
    while True:
        load_db_embeddings()
        time.sleep(REFRESH_INTERVAL)


threading.Thread(target=refresher, daemon=True).start()


def get_input_embedding(file_bytes):
    img = Image.open(io.BytesIO(file_bytes)).convert("RGB")
    face = mtcnn(img)
    if face is None:
        return None
    face = face.unsqueeze(0).to(device)
    with torch.no_grad():
        emb = resnet(face).cpu().numpy()[0]
    emb = emb / np.linalg.norm(emb)
    return emb


def match_face(emb, threshold=0.60):
    if emb is None:
        return None, None
    best_nisn = None
    best_sim = threshold
    with CACHE_LOCK:
        for nisn, db_emb in db_embeddings.items():
            sim = float(np.dot(db_emb, emb))
            if sim > best_sim:
                best_sim = sim
                best_nisn = nisn
    return best_nisn, best_sim


@app.route("/health")
def health():
    return jsonify({"status": "OK"}), 200


@app.route("/embed", methods=["POST"])
def embed_single():
    nisn = request.form.get("nisn")
    file = request.files.get("file")
    if not nisn or not file:
        return jsonify({"error": "nisn or file missing"}), 400

    try:
        img_bytes = file.read()
        emb = get_input_embedding(img_bytes)
        if emb is None:
            return jsonify({"error": "no_face"}), 422

        with CACHE_LOCK:
            db_embeddings[nisn] = emb / np.linalg.norm(emb)
        app.logger.info("Updated embedding for %s", nisn)
        return jsonify({"success": True, "nisn": nisn}), 200

    except Exception as e:
        app.logger.error(f"Failed embedding {nisn}: {e}")
        return jsonify({"error": "server_error"}), 500


@app.route("/classify", methods=["POST"])
def classify():
    if "file" not in request.files:
        app.logger.error("No file received in request.")
        return jsonify({"error": "No file"}), 400

    blob = request.files["file"].read()
    app.logger.info("File received for classification.")

    emb = get_input_embedding(blob)
    if emb is None:
        app.logger.warning("No face detected in the image.")
        return jsonify({"label": None, "confidence": 0.0, "method": "no_face"}), 200

    app.logger.info(f"Embedding generated: {emb[:5]}... (truncated for logging)")

    nisn, conf = match_face(emb, threshold=0.60)
    app.logger.info(
        f"Match result: nisn={nisn}, confidence={conf}, total_db={len(db_embeddings)}"
    )
    if nisn:
        method = "cosine"
    else:
        method = "unknown"
    return jsonify({"label": nisn, "confidence": conf or 0.0, "method": method}), 200


if __name__ == "__main__":
    load_db_embeddings()
    app.run(host="0.0.0.0", port=5000)
