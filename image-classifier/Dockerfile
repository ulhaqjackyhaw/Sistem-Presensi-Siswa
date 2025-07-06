# Stage 1: build deps
FROM python:3.11-slim-buster AS builder
ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get update \
 && apt-get install -y --no-install-recommends \
      build-essential python3-dev zlib1g-dev libjpeg-dev libpng-dev libtiff-dev libwebp-dev libopenjp2-7-dev \
 && rm -rf /var/lib/apt/lists/*

WORKDIR /app
COPY requirements.txt .
RUN pip install --upgrade pip wheel \
 && pip install --no-cache-dir -r requirements.txt

# Stage 2: runtime
FROM python:3.11-slim-buster AS runtime
ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get update \
 && apt-get install -y --no-install-recommends \
      zlib1g libjpeg62-turbo libpng16-16 libtiff5 libwebp6 libopenjp2-7 \
 && rm -rf /var/lib/apt/lists/*

WORKDIR /app
COPY --from=builder /usr/local/lib/python3.11/site-packages /usr/local/lib/python3.11/site-packages
COPY . .

HEALTHCHECK --interval=30s --timeout=5s --retries=3 \
  CMD curl -f http://localhost:5000/health || exit 1

EXPOSE 5000
CMD ["python", "app.py"]
