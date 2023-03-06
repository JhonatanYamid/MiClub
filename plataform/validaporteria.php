<?
include("procedures/general.php");
include("cmp/seo.php");
?>
<script src="assets/js/jquery.2.1.1.min.js"></script>
<style>
    body {
        background-color: #0000006e;
    }

    .modal-porteria {
        width: 100vw;
        height: 100vh;
        min-width: 100vw;
        min-height: 100vh;
        position: relative;
        background-color: transparent;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        width: 30vw;
        height: auto;
        min-height: 150px;
        min-width: 300px;
        background-color: #fff;
        border-radius: 1rem;
        text-align: center;
        padding: 1rem;
        justify-content: space-between;
    }
</style>
</head>

<body>
    <div class="modal-porteria">
        <div class="modal-content">
            <h4>Seleccione el puesto de porter&iacute;a para hoy</h4>
            <hr>
            <div class="form-group first ">
                <div class="col-xs-12">
                    <label class="col-sm-4 control-label no-padding-right" for="Nombre"> Nombre </label>
                    <div class="col-sm-8">
                        <select name="porteria" id="porteria" class="col-xs-12 mandatory">
                            <option value="Porteria Principal">Porteria Principal</option>
                            <option value="Porteria Servicios">Porteria Servicios</option>
                            <option value="Porteia Tezones">Porteia Tezones</option>
                            <option value="Porteria Comunitaria">Porteria Comunitaria</option>
                            <option value="formatos">formatos</option>
                            <option value="Drive">Drive</option>
                            <option value="cesco">cesco</option>
                            <option value="CCTV">CCTV</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <script>
            $(document).ready(function() {
                $('#porteria').change(function() {
                    var porteria = $(this).val();
                    var valida = confirm(porteria)
                    if (valida == true) {
                        $.post('includes/async/set_porteria.async.php', {
                                'Porteria': porteria,
                            }, function() {})
                            .done(function(response) {

                                console.log(response);
                                console.log("Porteria ok!");
                                window.location.href = "accesoinvitado.php";
                            })
                            .fail(function() {
                                console.log('Error porteria');
                            });
                    } else {}
                });
            });
        </script>
</body>

</html>