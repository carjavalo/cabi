<x-guest-layout>
    <div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem;">
        <div style="max-width: 550px; width: 100%;">
            {{-- Header corporativo --}}
            <div style="background: linear-gradient(135deg, #2e3a75 0%, #1a2255 100%); border-radius: 12px 12px 0 0; padding: 2rem; text-align: center; color: white;">
                <i class="fas fa-envelope-open-text" style="font-size: 3rem; margin-bottom: 0.75rem; opacity: 0.9;"></i>
                <h2 style="margin: 0; font-size: 1.5rem; font-weight: 700;">Verifica tu Correo Electrónico</h2>
                <p style="margin: 0.5rem 0 0; font-size: 0.85rem; opacity: 0.85;">Plan Institucional de Capacitación - HUV</p>
            </div>

            {{-- Cuerpo --}}
            <div style="background: #fff; border-radius: 0 0 12px 12px; box-shadow: 0 8px 30px rgba(0,0,0,0.12); padding: 2rem;">
                <div style="background: #f0f4ff; border-left: 4px solid #2e3a75; border-radius: 6px; padding: 1rem; margin-bottom: 1.5rem;">
                    <p style="margin: 0; color: #333; font-size: 0.9rem; line-height: 1.6;">
                        <strong>¡Gracias por registrarte!</strong><br>
                        Antes de comenzar, debes verificar tu dirección de correo electrónico haciendo clic en el enlace que te acabamos de enviar.
                        Si no recibiste el correo, con gusto te enviaremos otro.
                    </p>
                </div>

                @if (session('status') == 'verification-link-sent')
                    <div style="background: #d4edda; border: 1px solid #c3e6cb; border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem; text-align: center;">
                        <i class="fas fa-check-circle" style="color: #28a745; font-size: 1.2rem;"></i>
                        <p style="margin: 0.5rem 0 0; color: #155724; font-size: 0.9rem; font-weight: 600;">
                            Se ha enviado un nuevo enlace de verificación a tu correo electrónico.
                        </p>
                    </div>
                @endif

                <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-top: 1rem;">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" style="background: linear-gradient(135deg, #2e3a75, #1a2255); color: white; border: none; padding: 0.65rem 1.5rem; border-radius: 8px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: all 0.3s;">
                            <i class="fas fa-paper-plane"></i> Reenviar Correo
                        </button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" style="background: transparent; border: 1px solid #dc3545; color: #dc3545; padding: 0.65rem 1.5rem; border-radius: 8px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: all 0.3s;">
                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                        </button>
                    </form>
                </div>

                <div style="text-align: center; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #eee;">
                    <p style="margin: 0; color: #888; font-size: 0.78rem;">
                        <i class="fas fa-info-circle"></i> Revisa también tu carpeta de spam o correo no deseado.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
