<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Registro de Visitas · IAFCJ</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="/favicon.svg">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: { brand: { 50:'#eef2ff',100:'#e0e7ff',500:'#6366f1',600:'#4f46e5',700:'#4338ca',900:'#312e81' } }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        input, select, textarea { font-size: 17px !important; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-b from-brand-900 via-brand-700 to-brand-500 flex flex-col">

    <header class="px-6 pt-10 pb-8 text-center text-white">
        <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-white/15 backdrop-blur">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 64 64" fill="none">
                <path d="M32 14 L32 50 M18 24 L46 24" stroke="#fff" stroke-width="6" stroke-linecap="round"/>
                <circle cx="32" cy="14" r="4" fill="#c7d2fe"/>
            </svg>
        </div>
        <h1 class="text-2xl font-extrabold tracking-tight">¡Bienvenido/a!</h1>
        @if($evento)
            <p class="mt-1 text-brand-100 text-sm">Registro de visitas · <span class="font-semibold">{{ $evento->nombre }}</span></p>
        @else
            <p class="mt-1 text-brand-100 text-sm">Registro de visitas</p>
        @endif
        <p class="mt-3 inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold text-white backdrop-blur">
            <span>📋</span> Visitas registradas: <span id="visitas-count-num">{{ $visitasCount }}</span>
        </p>
    </header>

    <main class="flex-1 px-4 pb-10">
        <div class="mx-auto w-full max-w-md rounded-3xl bg-white shadow-xl shadow-brand-900/20 p-6">

            @if(!$evento)
                <div class="mb-5 rounded-2xl bg-amber-50 border border-amber-200 px-4 py-3 text-amber-800 text-sm">
                    No hay un evento activo configurado. El registro se guardará sin evento asignado.
                </div>
            @endif

            <div id="offline-banner" class="hidden mb-5 rounded-2xl bg-slate-100 border border-slate-200 px-4 py-3 text-slate-600 text-sm flex items-center gap-2">
                <span>📡</span>
                <span id="offline-banner-text">Sin conexión: tus registros se guardarán y enviarán cuando vuelva el internet.</span>
            </div>

            <form method="POST" action="{{ route('registro.store') }}" class="space-y-5">
                @csrf

                <div class="rounded-2xl bg-brand-50 border border-brand-100 p-4">
                    <label class="block text-sm font-semibold text-brand-700 mb-1.5">🧑‍💼 Ujier que registra *</label>

                    <input type="hidden" name="ujier_nombre" id="ujier_nombre" value="{{ old('ujier_nombre') }}">

                    {{-- Vista bloqueada: ujier ya elegido en este dispositivo --}}
                    <div id="ujier_locked" class="hidden items-center justify-between rounded-2xl border border-brand-200 bg-white px-4 py-3.5">
                        <span class="font-semibold text-brand-700">👤 <span id="ujier_locked_name"></span></span>
                        <button type="button" id="ujier_change" class="text-sm font-semibold text-brand-500 hover:text-brand-700 underline">Cambiar</button>
                    </div>

                    {{-- Selector --}}
                    <div id="ujier_picker">
                        <div class="relative">
                            <input type="text" id="ujier_search" autocomplete="off" placeholder="Escribe tu nombre para buscar..."
                                class="w-full rounded-2xl border border-brand-200 bg-white px-4 py-3.5 text-slate-900 placeholder-slate-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">

                            <div id="ujier_results" class="hidden absolute z-20 mt-2 w-full overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl">
                                <div id="ujier_results_list" class="max-h-52 overflow-y-auto"></div>
                                <button type="button" id="ujier_otro_btn" class="flex w-full items-center gap-2 border-t border-slate-100 px-4 py-3 text-left text-brand-600 hover:bg-brand-50 transition">
                                    <span class="text-base">✏️</span>
                                    <span class="text-sm font-semibold">No estoy en la lista</span>
                                </button>
                            </div>
                        </div>

                        <div id="ujier_otro_wrapper" class="hidden mt-2 flex gap-2">
                            <input type="text" id="ujier_otro_input" placeholder="Escribe tu nombre"
                                class="flex-1 rounded-2xl border border-brand-200 bg-white px-4 py-3.5 text-slate-900 placeholder-slate-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
                            <button type="button" id="ujier_otro_ok" class="rounded-2xl bg-brand-600 hover:bg-brand-700 text-white font-bold px-5 transition">Listo</button>
                        </div>
                    </div>

                    <p class="mt-1.5 text-xs text-brand-600">Tu nombre se queda guardado en este dispositivo para los siguientes registros.</p>
                    <p id="ujier_required_error" class="hidden mt-1.5 text-xs font-medium text-red-500">Selecciona o escribe tu nombre antes de registrar.</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nombre completo *</label>
                    <input type="text" name="nombre" id="nombre" autofocus value="{{ old('nombre') }}"
                        placeholder="Ej. Juan Pérez García"
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-slate-900 placeholder-slate-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 focus:bg-white outline-none transition">
                    <p id="nombre_error" class="hidden mt-1.5 text-xs font-medium text-red-500">😊 Cuéntanos cómo se llama la visita.</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Teléfono / WhatsApp</label>
                    <input type="tel" name="telefono" id="telefono" inputmode="numeric" maxlength="10" value="{{ old('telefono') }}"
                        placeholder="10 dígitos"
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-slate-900 placeholder-slate-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 focus:bg-white outline-none transition">
                    <p id="telefono_error" class="hidden mt-1.5 text-xs font-medium text-red-500">📱 Si lo agregas, el teléfono debe tener 10 dígitos.</p>
                </div>

                <div class="pt-1 border-t border-slate-100"></div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">¿Quién lo invitó? <span class="font-normal text-slate-400">(opcional)</span></label>

                    <input type="hidden" name="padre_espiritual_id" id="padre_espiritual_id" value="{{ old('padre_espiritual_id') }}">

                    <div class="relative">
                        <div class="relative">
                            <svg id="padre_search_icon" class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="7"/>
                                <path d="m20 20-3-3"/>
                            </svg>
                            <input type="text" id="padre_search" autocomplete="off" placeholder="Escribe un nombre para buscar..."
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50 pl-11 pr-10 py-3.5 text-slate-900 placeholder-slate-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 focus:bg-white outline-none transition">
                            <button type="button" id="padre_clear" class="hidden absolute right-3 top-1/2 -translate-y-1/2 h-7 w-7 flex items-center justify-center rounded-full text-slate-400 hover:bg-slate-200 hover:text-slate-600 transition">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M6 6l12 12M18 6 6 18"/></svg>
                            </button>
                        </div>

                        <div id="padre_results" class="hidden absolute z-20 mt-2 w-full overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl">
                            <div id="padre_results_list" class="max-h-52 overflow-y-auto"></div>
                            <button type="button" data-action="nadie" class="flex w-full items-center gap-2 border-t border-slate-100 px-4 py-3 text-left text-slate-600 hover:bg-slate-50 transition">
                                <span class="text-base">🤷</span>
                                <span class="text-sm font-medium">No sé / nadie en particular</span>
                            </button>
                            <button type="button" data-action="otro" class="flex w-full items-center gap-2 border-t border-slate-100 px-4 py-3 text-left text-brand-600 hover:bg-brand-50 transition">
                                <span class="text-base">✏️</span>
                                <span class="text-sm font-semibold">No está en la lista, escribir nombre</span>
                            </button>
                        </div>
                    </div>

                    <div id="padre_chip" class="hidden mt-2 inline-flex items-center gap-2 rounded-full bg-brand-50 border border-brand-100 px-3 py-1.5 text-sm font-semibold text-brand-700">
                        <span>👤</span>
                        <span id="padre_chip_label"></span>
                    </div>
                    <p id="padre_nadie_note" class="hidden mt-1.5 text-sm text-slate-400">No se asignará un padre/madre espiritual.</p>
                </div>

                <div id="otro_wrapper" class="hidden">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Escribe el nombre</label>
                    <input type="text" name="padre_espiritual_otro" id="padre_espiritual_otro" value="{{ old('padre_espiritual_otro') }}"
                        placeholder="Nombre del padre/madre espiritual"
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-slate-900 placeholder-slate-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 focus:bg-white outline-none transition">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">🏷️ Tipo de necesidad <span class="font-normal text-slate-400">(opcional, define el color del gafete)</span></label>

                    <input type="hidden" name="necesidad" id="necesidad">

                    <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                        @foreach(\App\Models\Visita::NECESIDADES as $key => $n)
                            <button type="button" data-necesidad="{{ $key }}"
                                class="necesidad-btn flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm font-semibold text-slate-700 transition hover:border-brand-300">
                                <span class="text-lg">{{ $n['emoji'] }}</span>
                                <span>{{ $n['label'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Notas (opcional)</label>
                    <textarea name="notas" rows="2" placeholder="Observaciones, peticiones de oración, etc."
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-slate-900 placeholder-slate-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 focus:bg-white outline-none transition">{{ old('notas') }}</textarea>
                </div>

                <button type="submit" id="submit-btn"
                    class="w-full rounded-2xl bg-brand-600 hover:bg-brand-700 active:scale-[0.98] text-white font-bold text-lg py-4 shadow-lg shadow-brand-600/30 transition disabled:opacity-60">
                    Registrar visita
                </button>
                <p id="pending-note" class="hidden text-center text-xs font-medium text-amber-600">
                    <span id="pending-count">0</span> registro(s) pendientes por enviar...
                </p>
            </form>
        </div>

        <p class="text-center text-brand-100 text-xs mt-6">IAFCJ · Sistema de registro de visitas</p>
    </main>

    <div id="success-overlay" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-brand-900/70 backdrop-blur-sm p-6">
        <div id="success-card" class="w-full max-w-sm rounded-3xl bg-white p-8 text-center shadow-2xl scale-90 opacity-0 transition-all duration-300">
            <div id="success-icon" class="mx-auto mb-5 flex h-24 w-24 items-center justify-center rounded-full bg-emerald-100">
                <svg class="h-14 w-14 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <path id="checkmark" d="M5 13l4 4L19 7" stroke-dasharray="24" stroke-dashoffset="24"/>
                </svg>
            </div>
            <h2 id="success-title" class="text-2xl font-extrabold text-slate-800">¡Registro exitoso!</h2>
            <p id="success-message" class="mt-2 text-slate-500">Gracias por registrar esta visita. Que Dios le bendiga 🙌</p>
            <button onclick="closeOverlay()" class="mt-6 w-full rounded-2xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-lg py-3.5 transition active:scale-[0.98]">
                Registrar otra visita
            </button>
        </div>
    </div>
    <style>
        @keyframes draw-check { to { stroke-dashoffset: 0; } }
        #success-card.show { transform: scale(1); opacity: 1; }
        #success-card.show #checkmark { animation: draw-check 0.5s ease-out 0.2s forwards; }
    </style>
    <script>
        function showOverlay({ message, duplicado, queued }) {
            const overlay = document.getElementById('success-overlay');
            const card = document.getElementById('success-card');
            const title = document.getElementById('success-title');
            const msg = document.getElementById('success-message');
            const icon = document.getElementById('success-icon');
            const check = document.getElementById('checkmark');

            if (queued) {
                title.textContent = '¡Registro guardado!';
                msg.textContent = 'No hay conexión por el momento. Se enviará automáticamente cuando vuelva el internet.';
                icon.className = 'mx-auto mb-5 flex h-24 w-24 items-center justify-center rounded-full bg-amber-100';
                check.parentElement.classList.replace('text-emerald-500', 'text-amber-500');
            } else if (duplicado) {
                title.textContent = '¡Información actualizada!';
                msg.textContent = message;
                icon.className = 'mx-auto mb-5 flex h-24 w-24 items-center justify-center rounded-full bg-brand-100';
                check.parentElement.classList.replace('text-emerald-500', 'text-brand-500');
            } else {
                title.textContent = '¡Registro exitoso!';
                msg.textContent = 'Gracias por registrar esta visita. Que Dios le bendiga 🙌';
                icon.className = 'mx-auto mb-5 flex h-24 w-24 items-center justify-center rounded-full bg-emerald-100';
                check.parentElement.classList.replace('text-amber-500', 'text-emerald-500');
                check.parentElement.classList.replace('text-brand-500', 'text-emerald-500');
            }

            overlay.classList.remove('hidden');
            check.style.animation = 'none';
            check.style.strokeDashoffset = '24';
            requestAnimationFrame(() => {
                card.classList.add('show');
                void check.offsetWidth;
                check.style.animation = '';
            });

            clearTimeout(window._overlayTimeout);
            window._overlayTimeout = setTimeout(closeOverlay, 4000);
        }

        function closeOverlay() {
            const overlay = document.getElementById('success-overlay');
            const card = document.getElementById('success-card');
            card.classList.remove('show');
            setTimeout(() => overlay.classList.add('hidden'), 200);
            document.querySelector('input[name="nombre"]').focus();
        }
    </script>

    <script>
        const padres = @json($padres->map(fn($p) => ['id' => $p->id, 'nombre' => $p->nombre]));

        const hiddenInput = document.getElementById('padre_espiritual_id');
        const searchInput = document.getElementById('padre_search');
        const searchIcon = document.getElementById('padre_search_icon');
        const resultsBox = document.getElementById('padre_results');
        const resultsList = document.getElementById('padre_results_list');
        const clearBtn = document.getElementById('padre_clear');
        const chip = document.getElementById('padre_chip');
        const chipLabel = document.getElementById('padre_chip_label');
        const nadieNote = document.getElementById('padre_nadie_note');
        const otroWrapper = document.getElementById('otro_wrapper');
        const otroInput = document.getElementById('padre_espiritual_otro');

        function normalize(str) {
            return str.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');
        }

        function renderResults(filter) {
            const term = normalize(filter || '');
            const matches = padres.filter(p => normalize(p.nombre).includes(term)).slice(0, 8);

            resultsList.innerHTML = '';
            if (matches.length === 0 && term !== '') {
                const empty = document.createElement('div');
                empty.className = 'px-4 py-3 text-sm text-slate-400';
                empty.textContent = 'Sin resultados, intenta otro nombre';
                resultsList.appendChild(empty);
            }
            matches.forEach(p => {
                const item = document.createElement('button');
                item.type = 'button';
                item.className = 'flex w-full items-center px-4 py-3 text-left text-slate-800 hover:bg-brand-50 transition border-b border-slate-50 last:border-b-0';
                item.textContent = p.nombre;
                item.addEventListener('click', () => selectPadre(p));
                resultsList.appendChild(item);
            });

            resultsBox.classList.remove('hidden');
        }

        function hideResults() {
            resultsBox.classList.add('hidden');
        }

        function resetSelection() {
            hiddenInput.value = '';
            otroInput.value = '';
            chip.classList.add('hidden');
            nadieNote.classList.add('hidden');
            otroWrapper.classList.add('hidden');
            searchInput.value = '';
            clearBtn.classList.add('hidden');
            searchInput.classList.remove('hidden');
            searchIcon.classList.remove('hidden');
        }

        function selectPadre(p) {
            resetSelection();
            hiddenInput.value = p.id;
            chipLabel.textContent = p.nombre;
            chip.classList.remove('hidden');
            searchInput.classList.add('hidden');
            searchIcon.classList.add('hidden');
            clearBtn.classList.remove('hidden');
            hideResults();
        }

        function selectNadie() {
            resetSelection();
            nadieNote.classList.remove('hidden');
            searchInput.classList.add('hidden');
            searchIcon.classList.add('hidden');
            clearBtn.classList.remove('hidden');
            hideResults();
        }

        function selectOtro() {
            resetSelection();
            hiddenInput.value = 'otro';
            otroWrapper.classList.remove('hidden');
            searchInput.classList.add('hidden');
            searchIcon.classList.add('hidden');
            clearBtn.classList.remove('hidden');
            hideResults();
            otroInput.focus();
        }

        searchInput.addEventListener('focus', () => renderResults(searchInput.value));
        searchInput.addEventListener('input', () => renderResults(searchInput.value));

        resultsBox.querySelector('[data-action="nadie"]').addEventListener('click', selectNadie);
        resultsBox.querySelector('[data-action="otro"]').addEventListener('click', selectOtro);

        clearBtn.addEventListener('click', () => {
            resetSelection();
            searchInput.focus();
        });

        document.addEventListener('click', (e) => {
            if (!e.target.closest('#padre_search') && !e.target.closest('#padre_results')) {
                hideResults();
            }
        });

        // Restore previous selection after a validation error
        @if(old('padre_espiritual_id') === 'otro')
            selectOtro();
            otroInput.value = @json(old('padre_espiritual_otro', ''));
        @elseif(old('padre_espiritual_id'))
            (() => {
                const prev = padres.find(p => String(p.id) === String(@json(old('padre_espiritual_id'))));
                if (prev) selectPadre(prev);
            })();
        @endif
    </script>

    <script>
        // --- Teléfono: solo dígitos, máx 10 ---
        const telefonoInput = document.getElementById('telefono');
        const telefonoError = document.getElementById('telefono_error');

        function validarTelefono() {
            const val = telefonoInput.value;
            const ok = val.length === 0 || val.length === 10;
            telefonoInput.classList.toggle('border-red-300', !ok);
            telefonoError.classList.toggle('hidden', ok);
            return ok;
        }

        telefonoInput.addEventListener('input', () => {
            telefonoInput.value = telefonoInput.value.replace(/\D/g, '').slice(0, 10);
            validarTelefono();
        });
        telefonoInput.addEventListener('blur', validarTelefono);

        // --- Nombre: requerido ---
        const nombreInput = document.getElementById('nombre');
        const nombreError = document.getElementById('nombre_error');

        function validarNombre() {
            const ok = nombreInput.value.trim().length > 0;
            nombreInput.classList.toggle('border-red-300', !ok);
            nombreError.classList.toggle('hidden', ok);
            return ok;
        }

        nombreInput.addEventListener('input', () => {
            if (nombreInput.value.trim().length > 0) {
                nombreInput.classList.remove('border-red-300');
                nombreError.classList.add('hidden');
            }
        });
        nombreInput.addEventListener('blur', validarNombre);

        // --- Tipo de necesidad: selección única tipo chip ---
        const necesidadInput = document.getElementById('necesidad');
        const necesidadBtns = document.querySelectorAll('.necesidad-btn');

        function selectNecesidad(btn) {
            const yaSeleccionado = btn.classList.contains('border-brand-500');
            necesidadBtns.forEach(b => {
                b.classList.remove('border-brand-500', 'bg-brand-50', 'ring-2', 'ring-brand-100');
                b.classList.add('border-slate-200', 'bg-slate-50');
            });
            if (yaSeleccionado) {
                necesidadInput.value = '';
            } else {
                btn.classList.remove('border-slate-200', 'bg-slate-50');
                btn.classList.add('border-brand-500', 'bg-brand-50', 'ring-2', 'ring-brand-100');
                necesidadInput.value = btn.dataset.necesidad;
            }
        }

        necesidadBtns.forEach(btn => btn.addEventListener('click', () => selectNecesidad(btn)));

        function resetNecesidad() {
            necesidadInput.value = '';
            necesidadBtns.forEach(b => {
                b.classList.remove('border-brand-500', 'bg-brand-50', 'ring-2', 'ring-brand-100');
                b.classList.add('border-slate-200', 'bg-slate-50');
            });
        }

        // --- Ujier: seleccionar de lista y bloquear en este dispositivo ---
        const ujieres = @json($ujieres->map(fn($u) => ['id' => $u->id, 'nombre' => $u->nombre]));
        const UJIER_KEY = 'iafcj_ujier_nombre';

        const ujierInput = document.getElementById('ujier_nombre');
        const ujierLocked = document.getElementById('ujier_locked');
        const ujierLockedName = document.getElementById('ujier_locked_name');
        const ujierChange = document.getElementById('ujier_change');
        const ujierPicker = document.getElementById('ujier_picker');
        const ujierSearch = document.getElementById('ujier_search');
        const ujierResults = document.getElementById('ujier_results');
        const ujierResultsList = document.getElementById('ujier_results_list');
        const ujierOtroBtn = document.getElementById('ujier_otro_btn');
        const ujierOtroWrapper = document.getElementById('ujier_otro_wrapper');
        const ujierOtroInput = document.getElementById('ujier_otro_input');
        const ujierOtroOk = document.getElementById('ujier_otro_ok');
        const ujierRequiredError = document.getElementById('ujier_required_error');

        function lockUjier(nombre) {
            ujierInput.value = nombre;
            localStorage.setItem(UJIER_KEY, nombre);
            ujierLockedName.textContent = nombre;
            ujierLocked.classList.remove('hidden');
            ujierLocked.classList.add('flex');
            ujierPicker.classList.add('hidden');
            ujierRequiredError.classList.add('hidden');
        }

        function unlockUjier() {
            ujierInput.value = '';
            localStorage.removeItem(UJIER_KEY);
            ujierLocked.classList.add('hidden');
            ujierLocked.classList.remove('flex');
            ujierPicker.classList.remove('hidden');
            ujierSearch.value = '';
            ujierOtroWrapper.classList.add('hidden');
            renderUjierResults('');
            ujierSearch.focus();
        }

        function normalizeUjier(str) {
            return str.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');
        }

        function renderUjierResults(filter) {
            const term = normalizeUjier(filter || '');
            const matches = ujieres.filter(u => normalizeUjier(u.nombre).includes(term)).slice(0, 8);

            ujierResultsList.innerHTML = '';
            if (matches.length === 0 && term !== '') {
                const empty = document.createElement('div');
                empty.className = 'px-4 py-3 text-sm text-slate-400';
                empty.textContent = 'Sin resultados';
                ujierResultsList.appendChild(empty);
            }
            matches.forEach(u => {
                const item = document.createElement('button');
                item.type = 'button';
                item.className = 'flex w-full items-center px-4 py-3 text-left text-slate-800 hover:bg-brand-50 transition border-b border-slate-50 last:border-b-0';
                item.textContent = u.nombre;
                item.addEventListener('click', () => lockUjier(u.nombre));
                ujierResultsList.appendChild(item);
            });

            ujierResults.classList.remove('hidden');
        }

        ujierSearch.addEventListener('focus', () => renderUjierResults(ujierSearch.value));
        ujierSearch.addEventListener('input', () => renderUjierResults(ujierSearch.value));

        ujierOtroBtn.addEventListener('click', () => {
            ujierResults.classList.add('hidden');
            ujierOtroWrapper.classList.remove('hidden');
            ujierOtroInput.focus();
        });

        ujierOtroOk.addEventListener('click', () => {
            const nombre = ujierOtroInput.value.trim();
            if (nombre) lockUjier(nombre);
        });

        ujierChange.addEventListener('click', unlockUjier);

        document.addEventListener('click', (e) => {
            if (!e.target.closest('#ujier_search') && !e.target.closest('#ujier_results')) {
                ujierResults.classList.add('hidden');
            }
        });

        // Estado inicial
        const savedUjier = localStorage.getItem(UJIER_KEY);
        if (savedUjier) {
            lockUjier(savedUjier);
        } else {
            renderUjierResults('');
        }

        // --- Envío con cola sin conexión ---
        const form = document.querySelector('form');
        const submitBtn = document.getElementById('submit-btn');
        const offlineBanner = document.getElementById('offline-banner');
        const pendingNote = document.getElementById('pending-note');
        const pendingCount = document.getElementById('pending-count');
        const visitasCountNum = document.getElementById('visitas-count-num');
        const QUEUE_KEY = 'iafcj_visitas_queue';

        function getQueue() {
            try {
                return JSON.parse(localStorage.getItem(QUEUE_KEY)) || [];
            } catch (e) {
                return [];
            }
        }

        function setQueue(queue) {
            localStorage.setItem(QUEUE_KEY, JSON.stringify(queue));
            pendingCount.textContent = queue.length;
            pendingNote.classList.toggle('hidden', queue.length === 0);
        }

        function updateOfflineBanner() {
            offlineBanner.classList.toggle('hidden', navigator.onLine);
        }

        function enviar(payload) {
            return fetch("{{ route('registro.store') }}", {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                },
                body: new URLSearchParams(payload),
            }).then(async res => {
                if (res.status === 422) {
                    const data = await res.json();
                    const err = new Error('validation');
                    err.validation = data.errors || {};
                    throw err;
                }
                if (!res.ok) throw new Error('http ' + res.status);
                return res.json();
            });
        }

        function mostrarErroresValidacion(errors) {
            if (errors.nombre) {
                nombreInput.classList.add('border-red-300');
                nombreError.classList.remove('hidden');
                nombreInput.focus();
            }
            if (errors.telefono) {
                telefonoInput.classList.add('border-red-300');
                telefonoError.classList.remove('hidden');
                if (!errors.nombre) telefonoInput.focus();
            }
        }

        function flushQueue() {
            const queue = getQueue();
            if (queue.length === 0 || !navigator.onLine) return;

            const [next, ...rest] = queue;
            enviar(next).then(data => {
                setQueue(rest);
                if (typeof data.visitasCount === 'number') {
                    visitasCountNum.textContent = data.visitasCount;
                }
                flushQueue();
            }).catch(() => {
                // sigue en la cola, se reintentará después
            });
        }

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            if (!ujierInput.value) {
                ujierRequiredError.classList.remove('hidden');
                ujierSearch.focus();
                return;
            }

            if (!validarNombre()) {
                nombreInput.focus();
                return;
            }

            if (!validarTelefono()) {
                telefonoInput.focus();
                return;
            }

            const payload = Object.fromEntries(new FormData(form).entries());
            submitBtn.disabled = true;

            enviar(payload).then(data => {
                if (typeof data.visitasCount === 'number') {
                    visitasCountNum.textContent = data.visitasCount;
                }
                showOverlay({ message: data.message, duplicado: data.duplicado, queued: false });
                form.reset();
                ujierInput.value = localStorage.getItem(UJIER_KEY) || '';
                resetSelection();
                resetNecesidad();
            }).catch((err) => {
                if (err.validation) {
                    mostrarErroresValidacion(err.validation);
                    return;
                }
                const queue = getQueue();
                queue.push(payload);
                setQueue(queue);
                updateOfflineBanner();
                showOverlay({ queued: true });
                form.reset();
                ujierInput.value = localStorage.getItem(UJIER_KEY) || '';
                resetSelection();
                resetNecesidad();
            }).finally(() => {
                submitBtn.disabled = false;
            });
        });

        window.addEventListener('online', () => {
            updateOfflineBanner();
            flushQueue();
        });
        window.addEventListener('offline', updateOfflineBanner);

        updateOfflineBanner();
        setQueue(getQueue());
        flushQueue();
    </script>
</body>
</html>
