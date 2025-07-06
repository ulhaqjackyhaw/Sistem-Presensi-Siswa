@extends('layouts.app')

@section('title', 'Presensi Siswa — Scan Wajah')

@push('css')
    <style>
        #message-container {
            min-height: 20px;
            line-height: 20px;
        }

        #toast-container {
            min-height: 50px;
            position: relative;
        }

        #toast-container .alert {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1050;
        }
    </style>
@endpush

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <h4 class="text-uppercase">Scan Wajah untuk Presensi</h4>
            <p>Kelas: <strong>{{ $class_label }}</strong> | Mata Pelajaran: <strong>{{ $subject_name }}</strong> | Waktu:
                <strong>
                    {{ \Carbon\Carbon::parse($start_time)->format('H:i') }} –
                    {{ \Carbon\Carbon::parse($end_time)->format('H:i') }}
                </strong>
            </p>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid text-center mb-3">
            <div class="mb-3">
                <p>
                    Waktu presensi akan ditutup pada:
                    <strong id="deadline" class="text-danger"></strong>
                    <br>
                    {{-- <span id="countdown" style="font-weight:bold;"></span> --}}
                    <span>Posisikan wajah Anda tepat di tengah kamera</span>
                </p>
            </div>
            <video id="video" width="600" height="350" autoplay playsinline style="border:1px solid #fff;"></video>
            <canvas id="canvas" width="160" height="160" style="display:none;"></canvas>
            <div id="message-container" class="m-2">
                <span id="message-placeholder" style="visibility: hidden;">Placeholder</span>
            </div>
            <div class="mt-3">
                <div id="toast-container" class="m-4">
                    <div id="toast-placeholder" style="visibility: hidden; height: 50px;"></div>
                </div>
                <button id="btn-scan" class="btn btn-primary">Mulai Scan</button>
                <button id="btn-stop" class="btn btn-danger">Berhenti Scan</button>
                {{--  --}}
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const startTime = "{{ $start_time }}";
            const endTime = "{{ $end_time }}";
            const graceMins = {{ $grace_minutes }};

            const todayDate = new Date().toISOString().slice(0, 10);
            const graceDeadline = new Date(`${todayDate}T${startTime}`);
            graceDeadline.setMinutes(graceDeadline.getMinutes() + graceMins);

            const endDeadline = new Date(`${todayDate}T${endTime}`);

            document.getElementById('deadline').textContent =
                graceDeadline.toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit'
                });

            function updateCountdown() {
                const now = new Date();
                let diff = graceDeadline - now;
                let text;
                if (diff > 0) {
                    const m = Math.floor(diff / 60000);
                    const s = Math.floor((diff % 60000) / 1000);
                    text = `Sisa waktu on–time: ${m}m ${s}s`;
                } else if (now < endDeadline) {
                    diff = endDeadline - now;
                    const m = Math.floor(diff / 60000);
                    const s = Math.floor((diff % 60000) / 1000);
                    text = `Masih bisa presensi selama ${m}m ${s}s`;
                } else {
                    text = `Waktu presensi telah berakhir`;
                    clearInterval(countdownInterval);
                    btn.disabled = true;
                }
                document.getElementById('countdown').textContent = text;
            }

            const countdownInterval = setInterval(updateCountdown, 1000);
            updateCountdown();
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const scheduleIds = @json($scheduleIds);
            // console.log('Schedule IDs:', scheduleIds);
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            const btnScan = document.getElementById('btn-scan');
            const btnStop = document.getElementById('btn-stop');
            const ctx = canvas.getContext('2d');
            let intervalId;
            const processedLabels = new Set(); // Set untuk melacak wajah yang sudah diproses

            navigator.mediaDevices.getUserMedia({
                    video: true
                })
                .then(stream => {
                    video.srcObject = stream;
                })
                .catch(err => showToast('Tidak dapat mengakses kamera: ' + err, 'danger'));

            btnScan.addEventListener('click', () => {
                btnScan.disabled = true;
                btnStop.disabled = false;
                showToast('🚀 Menyiapkan scan...', 'info');

                intervalId = setInterval(async () => {
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                    canvas.toBlob(async blob => {
                        const form = new FormData();
                        form.append('file', blob, 'snapshot.jpg');

                        try {
                            const resp1 = await fetch(
                                'http://localhost:5000/classify', {
                                    method: 'POST',
                                    body: form
                                });
                            const scan1 = await resp1.json();

                            console.log('Flask Response:', scan1);

                            if (scan1.label) {
                                scanMessage('✅ Wajah dikenali.', 'success');
                            }

                            if (scan1.label) {
                                processedLabels.add(scan1.label);
                                console.log('Dikenali oleh Flask:', scan1);

                                const resp2 = await fetch(
                                    "{{ route('manage-attendances.store') }}", {
                                        method: 'POST',
                                        headers: {
                                            'Accept': 'application/json',
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        },
                                        body: JSON.stringify({
                                            class_schedule_id: scheduleIds[
                                                0],
                                            nisn: scan1.label,
                                            meeting_date: new Date()
                                                .toISOString().slice(0,
                                                    10)
                                        })
                                    });
                                // console.log('Response Laravel status:', resp2
                                //     .status);

                                const scan2 = await resp2.json();
                                // console.log('Response Laravel body:', scan2);

                                // Menampilkan pesan berdasarkan respons dari Laravel
                                if (resp2.ok) {
                                    showToast(scan2.message, 'success');
                                } else if (resp2.status === 409) {
                                    showToast(scan2.message, 'success');
                                } else if (resp2.status === 500) {
                                    showToast(scan2.message, 'danger');
                                } else {
                                    showToast('Terjadi kesalahan tidak terduga.',
                                        'danger');
                                }
                            } else if (scan1.method === 'no_face') {
                                scanMessage(
                                    '📷 Wajah tidak terdeteksi, coba lagi...',
                                    'warning');
                            } else if (!scan1.label) {
                                scanMessage('❓ Wajah tidak dikenal.', 'danger');
                            }
                        } catch (err) {
                            console.error(err);
                            showToast('Terjadi kesalahan saat memproses data: ' +
                                err, 'danger');
                        }
                    }, 'image/jpeg');
                }, 1000);
            });

            btnStop.addEventListener('click', () => {
                btnScan.disabled = false;
                btnStop.disabled = true;
                clearInterval(intervalId);
                showToast('🛑 Pemindaian dihentikan.', 'info');
            });

            function scanMessage(message, type = 'info') {
                const container = document.getElementById('message-container');
                const placeholder = document.getElementById('message-placeholder');
                let color;

                switch (type) {
                    case 'success':
                        color = '#1a8754';
                        break;
                    case 'danger':
                        color = '#db3545';
                        break;
                    case 'warning':
                        color = '#ffc107';
                        break;
                    default:
                        color = '#0d6efd';
                }

                placeholder.style.visibility = 'visible';
                placeholder.style.color = color;
                placeholder.textContent = message;

                setTimeout(() => {
                    placeholder.style.visibility = 'hidden';
                    placeholder.textContent = 'Placeholder';
                }, 2000);
            }

            function showToast(message, type = 'info') {
                const id = 'toast-' + Date.now();
                const html = `
      <div id="${id}" class="alert alert-${type} alert-dismissible fade show" role="alert">
        ${message}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
      </div>`;
                document.getElementById('toast-container').insertAdjacentHTML('beforeend', html);
                setTimeout(() => {
                    const toast = document.getElementById(id);
                    if (toast) {
                        toast.remove();
                    }
                }, 2000);
            }
        });
    </script>
@endpush
