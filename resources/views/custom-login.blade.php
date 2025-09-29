<div>
    <style>


        .login-container {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 25px;
            padding: 50px 40px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            width: 100%;
            max-width: 450px;
            text-align: center;
        }

        .logo h1 {
            color: #0c7b93;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .logo p {
            color: #718096;
            font-size: 16px;
            font-weight: 300;
        }

        .forgot-password a {
            color: #00a8cc;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        .forgot-password a:hover {
            color: #0c7b93;
        }

        /* Styling untuk submit button */
        .fi-btn {
            background: linear-gradient(135deg, #0c7b93 0%, #00a8cc 100%) !important;
            border: none !important;
            border-radius: 12px !important;
            padding: 12px 24px !important;
            font-weight: 600 !important;
            font-size: 16px !important;
            transition: all 0.3s ease !important;
            box-shadow: 0 4px 15px rgba(12, 123, 147, 0.3) !important;
        }

        .fi-btn:hover {
            background: linear-gradient(135deg, #00a8cc 0%, #0c7b93 100%) !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 20px rgba(12, 123, 147, 0.4) !important;
        }

        .fi-btn:active {
            transform: translateY(0) !important;
        }
    </style>


            <div class="logo">
                <img src="{{ asset('storage/logo_SDI.svg') }}" alt="SDI Logo" style="width: 100px; height: 100px; margin-bottom: 15px;">
                <h1>SDI Stock Taking</h1>
                <p>Welcome back! Please sign in to your account</p>
            </div>

            <form wire:submit="authenticate">
                {{ $this->form }}

                <!-- Manual submit button -->
                <div style="margin-top: 20px;">
                    <button type="submit" class="fi-btn" style="width: 100%;">
                        Sign In
                    </button>
                </div>
            </form>



</div>
