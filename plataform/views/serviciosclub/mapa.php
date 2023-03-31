<div id="ServicioMapa">
    <!DOCTYPE html>
    <html>

    <head>
        <meta charset="UTF-8" />
        <title>My Page</title>
        <style>
            .loader {
                border: 16px solid #f3f3f3;
                border-radius: 50%;
                border-top: 16px solid #3498db;
                width: 120px;
                height: 120px;
                -webkit-animation: spin 2s linear infinite;
                /* Safari */
                animation: spin 2s linear infinite;
            }

            /* Safari */
            @-webkit-keyframes spin {
                0% {
                    -webkit-transform: rotate(0deg);
                }

                100% {
                    -webkit-transform: rotate(360deg);
                }
            }

            @keyframes spin {
                0% {
                    transform: rotate(0deg);
                }

                100% {
                    transform: rotate(360deg);
                }
            }
        </style>

    </head>

    <body>
        <h1>Reservations map</h1>
        <div>
            <p>1. Use la rueda del mouse para hacer zoom</p>
            <p>2. Use (OPT o ALT) + arrastrar mouse para hacer panning</p>
        </div>
        <div>
            <!-- <label>1. Seleccione el servicio a configurar </label> -->
            <select id="service-selector" style="display:none;"></select>
        </div>
        <canvas id="canvas-root" width="500" height="400"></canvas>
        <div id="loader"></div>
        <!-- <button id="remove-all-button">Remove all</button> -->
        <div>
            <p id="zoom-level"></p>
        </div>
        <div>
            <p id="map-image-size"></p>
        </div>
        <div>
            <label>Seleccione la imagen del mapa</label>
            <input type="file" name="map-image" id="map-image-input">
            <input type="hidden" name="IDClub" id="IDClub" value = "<?= SIMUser::get("club"); ?>">
            <input type="hidden" name="url" id="url" value = "<?= URLROOT; ?>">
            <input type="hidden" name="IDServicio" id="IDServicio" value = "<?= SIMNet::get("ids"); ?>">
        </div>
        <button id="map-image-upload">Cargar imagen</button>
        <div>
            <button id="save-map">Guardar mapa</button>
        </div>
        <div>
            <button id="load-map">Cargar mapa</button>
        </div>

        <!-- <script type="module" src="./editor.ts"></script> -->

        <!-- <div id="canvas-root"></div> -->

        <!-- <script type="module" src="index.js"></script> -->

        <!-- Change then for the production scripts -->
        <!-- <script src="https://unpkg.com/react@18/umd/react.development.js" crossorigin></script>
    <script src="https://unpkg.com/react-dom@18/umd/react-dom.development.js" crossorigin></script>
    <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script> -->
        <script src="https://cdn.jsdelivr.net/npm/fabric" crossorigin></script>
        <script type="module" src="js/mapas/editor.js"></script>

        <!-- <script src="index.js" type="text/babel"></script>
    <script src="canvas.js" type="text/babel"></script> -->
    </body>

    </html>

</div>