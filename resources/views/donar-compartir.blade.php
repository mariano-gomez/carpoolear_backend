<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Donar a Carpoolear</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,500,600' rel='stylesheet' type='text/css'>
        <style>
            html, body {
                background: #FAFAFA;
            }
            h3 {
                font-weight: normal;
            }
            .container {
                margin-top: 3rem;
            }
            .donation {
                margin-top: 4em;
                margin-bottom: 1em;
                color: #666;
                font-size: 16px;
            }
            .donation-top {
                margin-top: 0;
            }
            .radio {
                margin-bottom: 1.5em;
            }
            .btn-donar {
                min-height: 5em;
                vertical-align: middle;
                border: 0;
                border-radius: 10px;
                margin-right: 10px;

                width: 43%;
                margin: 2%;
                padding: 1em 0;
                white-space: normal;
                max-width: 250px;
            }
            .btn-donar:hover,
            .btn-donar:active,
            .btn-donar:focus {
                opacity: 0.90;
            }
            .btn-unica-vez {    
                color: #fff;
                background-color: #5cb85c;
                border-color: #4cae4c;
            }
            .btn-mensualmente {    
                color: #fff;
                background-color: #5bc0de;
                border-color: #46b8da;
            }
            .radio-inline > * {
                vertical-align: middle;
            }
            .radio-inline input {
                margin-right: .5rem;
            }
            .radio-inline span {
                margin-right: 1.5rem;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="card">
                <div class="card-header bg-transparent text-center">
                    <h3>
                        Doná a Carpoolear 
                        <br class="d-md-none d-lg-none d-xl-none">
                        un proyecto de <img class="flush-right" src="/img/logo_sts_nuevo_color.png" width="170" height="50" alt="STS Rosario">
                    </h3>
                </div>
                <div class="card-body text-center">
                    <div class="donation donation-top">
                        <div class="radio">
                            <label class="radio-inline">
                                <input type="radio" name="donationValor" id="donation50" value="200" v-model="donateValue"><span>$ 200</span>
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="donationValor" id="donation100" value="400" v-model="donateValue"><span>$ 400</span>
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="donationValor" id="donation200" value="1000" v-model="donateValue"><span>$ 1000</span>
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="donationValor" id="donation500" value="0" v-model="donateValue"><span>Elige tu propia aventura (solo mensual)</span>
                            </label>
                        </div>
                        <div>
                            <button class="btn-unica-vez btn-donar btn-unica" id="btn-unica">ÚNICA VEZ</button>
                            <button class="btn-mensualmente btn-donar" id="btn-mensual">MENSUAL <br />(cancelá cuando quieras)</button>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="/donar" class="card-link">Por qué donar a Carpoolear</a>
                </div>
            </div>
        </div>
    </body>
    <script>
        function post (user, ammount) {
            var http = new XMLHttpRequest();
            var url = '/api/users/donation';
            var params = 'has_donated=1&ammount=' + ammount + '&user=' + user;
            http.open('POST', url, true);
    
            //Send the proper header information along with the request
            http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    
            http.onreadystatechange = function() {//Call a function when the state changes.
                if(http.readyState == 4 && http.status == 200) {
                    console.log('success');
                }
            }
            http.send(params);
        }
        function getParameterByName(name, url) {
            if (!url) url = window.location.href;
            name = name.replace(/[\[\]]/g, '\\$&');
            var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
                results = regex.exec(url);
            if (!results) return null;
            if (!results[2]) return '';
            return decodeURIComponent(results[2].replace(/\+/g, ' '));
        }
        var linksUnicaVez = {
            200: "https://www.mercadopago.com.ar/checkout/v1/redirect?preference-id=201279444-f94a3145-7336-4d79-9eb9-76c5402894fa",
            400: "https://www.mercadopago.com.ar/checkout/v1/redirect?preference-id=201279444-42de1d74-f967-455f-80bf-a7a77650db06",
            1000: "https://www.mercadopago.com.ar/checkout/v1/redirect?preference-id=201279444-c693bd88-7fd4-49d8-9f22-2b80151d184e",
            0: ""
        };
        var linksMensual = {
            200: "http://mpago.la/2k6JFz6",
            400: "http://mpago.la/1FE4px6",
            1000: "http://mpago.la/1EcA6f4",
            0: "http://mpago.la/2XdoxpF"
        };
        var btns = document.querySelectorAll(".btn-donar");
        btns.forEach(function (btn) {
            btn.addEventListener("click", function (event) {
                var rdb = document.querySelector('input[name="donationValor"]:checked');
                if (rdb) {
                    var value = rdb.value;
                    if (event.target.className.indexOf("btn-unica") >= 0) {
                        window.open(linksUnicaVez[value], '_blank');
                    } else {
                        window.open(linksMensual[value], '_blank');
                    }
                    var user_id = getParameterByName('u');
                    post(user_id, value);
                } else {
                    alert("Debes seleccionar un monto de donación. Gracias!");
                }
            });
        });
    </script>
</html>