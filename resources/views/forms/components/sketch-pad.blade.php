<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div
        x-data="{
            state: $wire.$entangle('{{ $getStatePath() }}'),
            signaturePad: null,

            init() {
                // Load the library dynamically if it doesn't exist
                if (!window.SignaturePad) {
                    let script = document.createElement('script');
                    script.src = 'https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js';
                    script.onload = () => this.initPad();
                    document.head.appendChild(script);
                } else {
                    this.initPad();
                }
            },

            initPad() {
                let canvas = this.$refs.canvas;

                // Initialize SignaturePad
                this.signaturePad = new SignaturePad(canvas, {
                    backgroundColor: 'rgba(255, 255, 255, 0)', // Transparent
                    penColor: 'rgb(0, 0, 0)',
                });

                // Handle Resizing (High DPI screens)
                this.resizeCanvas();
                window.addEventListener('resize', () => this.resizeCanvas());

                // Load existing data if available
                if (this.state) {
                    this.signaturePad.fromDataURL(this.state);
                }

                // Sync with Filament on change
                this.signaturePad.addEventListener('endStroke', () => {
                    this.state = this.signaturePad.toDataURL('image/png');
                });
            },

            resizeCanvas() {
                let canvas = this.$refs.canvas;
                let ratio = Math.max(window.devicePixelRatio || 1, 1);

                // Helper to save data before resize clears it
                let data = this.signaturePad ? this.signaturePad.toDataURL() : null;

                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext('2d').scale(ratio, ratio);

                // Restore data
                if (this.signaturePad) {
                    this.signaturePad.clear();
                    if (data && data !== 'data:,' && data.length > 100) {
                        this.signaturePad.fromDataURL(data);
                    }
                }
            },

            clear() {
                this.signaturePad.clear();
                this.state = null;
            }
        }"
        class="w-full"
    >
        <!-- Canvas Container -->
        <div
            class="border border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden bg-white dark:bg-gray-900 shadow-sm"
            style="height: 200px;"
        >
            <canvas
                x-ref="canvas"
                class="w-full h-full touch-none"
            ></canvas>
        </div>

        <!-- Clear Button -->
        <div class="mt-2 flex justify-end">
            <button
                type="button"
                @click="clear()"
                class="text-sm text-danger-600 hover:text-danger-500 font-medium px-2 py-1 transition"
            >
                Clear Signature
            </button>
        </div>
    </div>
</x-dynamic-component>
