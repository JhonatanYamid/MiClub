// import { fabric } from 'https://cdn.jsdelivr.net/npm/fabric';
// const BASE_URL = "https://www.miclubapp.com/services/club.php";
// const BASE_URL = "http://192.168.5.110:8080/services";
var URLBASE = document.getElementById("url").value;
var BASE_URL = URLBASE + "services/club.php";
var CLUB_ID = document.getElementById("IDClub").value;
var SERVICIO_ID = document.getElementById("IDServicio").value;
var APP_VERSION = "33";
var USER = 'tokengenerico';
var PASS = 'M1Club123*';
var TOKENID = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczpcL1wvd3d3Lm1pY2x1YmFwcC5jb20iLCJhdWQiOiJodHRwczpcL1wvd3d3Lm1pY2x1YmFwcC5jb20iLCJpYXQiOjE2Nzk4NzQ0MDksIm5iZiI6MTY3OTg3NDQwOSwiZXhwIjoxNjc5OTM0NDA5LCJkYXRhIjp7IklEVXN1YXJpb1dTIjoiMjQiLCJOb21icmUiOiJUb2tlbkdlbmVyaWNvIiwiRW1wcmVzYSI6IlRPRE9TIn19.mAvRb9I8g3sgfNQ_fPYppmE6OdBbC8YAN29U354g3-w";
var ClubReservationMapConfiguration = /** @class */ (function () {
    function ClubReservationMapConfiguration() {
        this.allShapes = [];
        this.service = null;
        this.mapConfig = null;
    }
    ClubReservationMapConfiguration.prototype.removeShape = function (shape) {
        //Remove the shape from the canvas
        for (var i = 0; i < this.allShapes.length; i++) {
            var existingShape = this.allShapes[i];
            if (shape.element.IDElemento == existingShape.element.IDElemento) {
                canvas.remove(existingShape.group);
                this.allShapes.splice(i, 1);
                break;
            }
        }
    };
    ClubReservationMapConfiguration.prototype.removeAllShapes = function () {
        //Remove the shapes from the canvas
        for (var i = 0; i < currentConfiguration.allShapes.length; i++) {
            var shape = currentConfiguration.allShapes[i];
            canvas.remove(shape.group);
        }
        //Clear allShapes array
        currentConfiguration.allShapes = Array();
    };
    return ClubReservationMapConfiguration;
}());
var canvas = new fabric.Canvas("canvas-root", {
    backgroundColor: 'rgb(0,80,150)',
    selectionColor: 'rgba(0,0,200, 1.0)',
    selectionLineWidth: 2
});
var currentConfiguration = new ClubReservationMapConfiguration();
var loaderElement = document.getElementById("loader");
var allServices = Array();
var initialViewPortTransfor = canvas.viewportTransform;
var zoomLabel = document.getElementById("zoom-level");
updateZoomLabel();
var mapImageSizeLabel = document.getElementById("map-image-size");
updateImageSizeLabel();
function updateZoomLabel() {
    if (zoomLabel !== null) {
        var zoom = canvas.getZoom();
        zoomLabel.textContent = "Zoom (".concat(Math.round(zoom * 100), "%)");
    }
}
function updateImageSizeLabel() {
    var _b, _c, _d, _e, _f, _g;
    if (mapImageSizeLabel !== null) {
        var width = (_d = (_c = (_b = currentConfiguration === null || currentConfiguration === void 0 ? void 0 : currentConfiguration.mapConfig) === null || _b === void 0 ? void 0 : _b.mapImage) === null || _c === void 0 ? void 0 : _c.width) !== null && _d !== void 0 ? _d : 0;
        var height = (_g = (_f = (_e = currentConfiguration === null || currentConfiguration === void 0 ? void 0 : currentConfiguration.mapConfig) === null || _e === void 0 ? void 0 : _e.mapImage) === null || _f === void 0 ? void 0 : _f.height) !== null && _g !== void 0 ? _g : 0;
        mapImageSizeLabel.textContent = "Tama\u00F1o mapa: [Ancho: ".concat(width, " - Alto: ").concat(height, "]");
    }
}
canvas.on('mouse:wheel', function (opt) {
    var delta = opt.e.deltaY;
    var zoom = canvas.getZoom();
    zoom *= Math.pow(0.999, delta);
    if (zoom > 20)
        zoom = 20;
    if (zoom < 0.01)
        zoom = 0.01;
    canvas.zoomToPoint({ x: opt.e.offsetX, y: opt.e.offsetY }, zoom);
    opt.e.preventDefault();
    opt.e.stopPropagation();
    updateZoomLabel();
});
canvas.on('mouse:down', function (opt) {
    var evt = opt.e;
    if (evt.altKey === true) {
        this.isDragging = true;
        this.selection = false;
        this.lastPosX = evt.clientX;
        this.lastPosY = evt.clientY;
    }
});
canvas.on('mouse:move', function (opt) {
    if (canvas.isDragging) {
        var e = opt.e;
        var vpt = canvas.viewportTransform;
        vpt[4] += e.clientX - canvas.lastPosX;
        vpt[5] += e.clientY - canvas.lastPosY;
        canvas.requestRenderAll();
        canvas.lastPosX = e.clientX;
        canvas.lastPosY = e.clientY;
    }
});
canvas.on('mouse:up', function (opt) {
    // on mouse up we want to recalculate new interaction
    // for all objects, so we call setViewportTransform
    canvas.setViewportTransform(canvas.viewportTransform);
    canvas.isDragging = false;
    canvas.selection = true;
});
function showLoading(show) {
    if (loaderElement !== null) {
        loaderElement.style.display = show ? "block" : "none";
    }
}
function addElementShape(elemet, left, top, width, height) {
    var _b;
    var index = currentConfiguration.allShapes.length;
    var rectWidth = width !== null && width !== void 0 ? width : 40;
    var rectHeight = height !== null && height !== void 0 ? height : 40;
    var rect = new fabric.Rect({
        fill: "rgba(0,200,0, 0.5)",
        originX: 'center',
        originY: 'center',
        width: rectWidth,
        height: rectHeight
    });
    var text = new fabric.Textbox((_b = elemet.Nombre) !== null && _b !== void 0 ? _b : elemet.IDElemento, {
        fontSize: 10,
        originX: 'center',
        originY: 'center',
        width: rectWidth,
        height: rectHeight,
        textAlign: 'center'
    });
    var leftIndex = index;
    var fixedLeft = leftIndex * (rectWidth + 10);
    var topIndex = Math.trunc(fixedLeft / canvas.width);
    var fixedTop = topIndex * (rectWidth + 10);
    fixedLeft = (fixedLeft - (topIndex * canvas.width));
    var group = new fabric.Group([rect, text], {
        top: top !== null && top !== void 0 ? top : fixedTop,
        left: left !== null && left !== void 0 ? left : fixedLeft
    });
    currentConfiguration.allShapes.push({ element: elemet, group: group });
    rect.set('selectable', true);
    canvas.add(group);
}
//Configure the remove all shapes button
var removeAllShapesButton = document.getElementById("remove-all-button");
if (removeAllShapesButton !== null) {
    removeAllShapesButton.onclick = function () {
        currentConfiguration.removeAllShapes();
    };
}
//Configure select service
var serviceSelector = document.getElementById("service-selector");
if (serviceSelector !== null) {
    //try to load the services
    showLoading(true);
    var isFirstElement = true;
    var newElement = document.createElement("option");
    newElement.setAttribute("value", SERVICIO_ID);
    var node = document.createTextNode('servicio');
    newElement.appendChild(node);
    serviceSelector.appendChild(newElement);
    //Load the first element because it is selected by default
    if (isFirstElement) {
        isFirstElement = false;
        var value = { IDServicio: SERVICIO_ID, Nombre: "", Icono: "" };
        requestAndPopulateElements(value, true);
    }
    serviceSelector.onchange = function () {
        var _b, _c;
        if (!confirm("¿Está seguro de cambiar el servicio a configurar?\nTodos los cambios no guardados se perderán")) {
            serviceSelector.value = (_c = (_b = currentConfiguration.service) === null || _b === void 0 ? void 0 : _b.IDServicio) !== null && _c !== void 0 ? _c : "";
            return;
        }
        var serviceId = serviceSelector.value;
        //Each time the services changes, populate again the canvas with the elements
        // const service = allServices.find(element => element.IDServicio == serviceId);
        var service = { IDServicio: SERVICIO_ID, Nombre: "", Icono: "" };
        if (service) {
            requestAndPopulateElements(service, true);
        }
    };
}
function requestAndPopulateElements(service, loadingMap) {
    //First remove all shapes
    currentConfiguration.removeAllShapes();
    //Request the elements by the service
    showLoading(true);
    currentConfiguration.service = service;
    makeGETApiRequest("getelementos", [{ key: "IDServicio", value: service.IDServicio }])
        .then(function (response) {
        var _b, _c, _d, _e;
        showLoading(false);
        var numResponses = (_c = (_b = response.response) === null || _b === void 0 ? void 0 : _b.length) !== null && _c !== void 0 ? _c : 0;
        if (!((_d = response.response) === null || _d === void 0 ? void 0 : _d.length)) {
            alert("No hay elementos configurados en el servicio, configure primero los elementos");
        }
        else {
            //Populate the elements
            for (var _i = 0, _f = (_e = response.response) !== null && _e !== void 0 ? _e : []; _i < _f.length; _i++) {
                var element = _f[_i];
                addElementShape(element);
            }
        }
        if (loadingMap) {
            requestPreviousMap();
        }
    })["catch"](function (error) {
        showLoading(false);
        console.log(error.message);
    });
}
//Configure map image input
var mapImageInput = document.getElementById("map-image-input");
var mapImageUploadButton = document.getElementById("map-image-upload");
if (mapImageInput !== null && mapImageUploadButton !== null) {
    mapImageUploadButton.onclick = function () {
        var _b;
        if (mapImageInput.files === null) {
            return;
        }
        if (!mapImageInput.files.length) {
            alert("Seleccione un archivo par ala imagen del mapa");
            return;
        }
        var serviceId = (_b = currentConfiguration.service) === null || _b === void 0 ? void 0 : _b.IDServicio;
        if (!serviceId) {
            alert("Primero seleccione un servicio");
            return;
        }
        showLoading(true);
        makePOSTApiRequest("setimagenmapaservicioreserva", [{ key: "IDServicio", value: serviceId }], mapImageInput.files)
            .then(function (response) {
            var _b;
            var imgUrl = (_b = response.response) === null || _b === void 0 ? void 0 : _b.url;
            if (imgUrl) {
                changeMapImage(imgUrl);
            }
        })["catch"](function (error) {
            console.log(error);
            console.error("Error uploading file: " + error);
        });
    };
}
function changeMapImage(imageUrl) {
    var _b;
    //Remove previous background image
    var mapImage = (_b = currentConfiguration.mapConfig) === null || _b === void 0 ? void 0 : _b.mapImage;
    if (mapImage) {
        canvas.remove(mapImage);
        currentConfiguration.mapConfig = null;
    }
    if (!imageUrl) {
        updateImageSizeLabel();
        return;
    }
    fabric.Image.fromURL(imageUrl, function (oImg) {
        currentConfiguration.mapConfig = {
            mapImage: oImg,
            width: oImg.width,
            height: oImg.height,
            imageUrl: imageUrl
        };
        canvas.add(oImg);
        var scaleWidthFactor = canvas.width / oImg.width;
        var scaleHeightFactor = canvas.height / oImg.height;
        var scaleFactor = Math.min(scaleWidthFactor, scaleHeightFactor);
        // oImg.scale(scaleFactor);
        oImg.set('selectable', false);
        if (initialViewPortTransfor) {
            canvas.setViewportTransform(initialViewPortTransfor);
        }
        canvas.zoomToPoint({ x: 0, y: 0 }, scaleFactor);
        canvas.sendToBack(oImg);
        updateZoomLabel();
        updateImageSizeLabel();
    });
}
function loadMapInCurrentConfiguration(shapes, mapInfo) {
    canvas.discardActiveObject();
    //Use a copy of the shapes to avoid issues when inserting new elements
    var copyShapes = currentConfiguration.allShapes.map(function (value) { return value; });
    var _loop_1 = function (existingShape) {
        var newShape = JSON.parse(shapes).find(function (shape) {
            if (shape.IDElemento == existingShape.element.IDElemento) {
                return shape;
            }
        });
        if (newShape) {
            //Remove the preivous one and create a new one
            currentConfiguration.removeShape(existingShape);
            //Create a new shape
            addElementShape(existingShape.element, newShape.PosicionX, newShape.PosicionY, newShape.Ancho, newShape.Alto);
        }
    };
    //Try to load the position and size for each existing shape
    for (var _i = 0, copyShapes_1 = copyShapes; _i < copyShapes_1.length; _i++) {
        var existingShape = copyShapes_1[_i];
        _loop_1(existingShape);
    }
    canvas.requestRenderAll();
    //Try to load map
    changeMapImage(mapInfo === null || mapInfo === void 0 ? void 0 : mapInfo.imageUrl);
}
function requestPreviousMap() {
    var service = currentConfiguration.service;
    if (!service) {
        alert("Primero debe seleccionar un servicio");
        return;
    }
    showLoading(true);
    makeGETApiRequest("getmapaservicioreserva", [{ key: "IDServicio", value: service.IDServicio }])
        .then(function (response) {
        var _b, _c, _d, _e;
        showLoading(false);
        var savedMap = response.response;
        var imageUrl = savedMap === null || savedMap === void 0 ? void 0 : savedMap.ImagenMapa;
        if (savedMap) {
            if (imageUrl) {
                loadMapInCurrentConfiguration((_b = savedMap.Elementos) !== null && _b !== void 0 ? _b : [], {
                    imageUrl: imageUrl,
                    width: (_c = savedMap.ImagenAncho) !== null && _c !== void 0 ? _c : 0,
                    height: (_d = savedMap.ImagenAlto) !== null && _d !== void 0 ? _d : 0
                });
            }
            else {
                loadMapInCurrentConfiguration((_e = savedMap.Elementos) !== null && _e !== void 0 ? _e : [], null);
            }
        }
    })["catch"](function (error) {
        showLoading(false);
        console.log(error);
        console.error(error);
    });
}
//Configure load map
var loadMapButton = document.getElementById("load-map");
if (loadMapButton !== null) {
    loadMapButton.onclick = function () {
        requestPreviousMap();
    };
}
//Configure save map
var saveMapButton = document.getElementById("save-map");
if (saveMapButton !== null) {
    saveMapButton.onclick = function () {
        var _b, _c;
        var params = [{ key: "IDServicio", value: (_c = (_b = currentConfiguration.service) === null || _b === void 0 ? void 0 : _b.IDServicio) !== null && _c !== void 0 ? _c : "" }];
        var elementsList = currentConfiguration.allShapes.map(function (shape) {
            return {
                "IDElemento": shape.element.IDElemento,
                "PosicionX": shape.group.left,
                "PosicionY": shape.group.top,
                "Ancho": shape.group.width * shape.group.scaleX,
                "Alto": shape.group.height * shape.group.scaleY
            };
        });
        params.push({ key: "Elementos", value: JSON.stringify(elementsList) });
        var mapInfo = currentConfiguration.mapConfig;
        if (mapInfo) {
            params.push({ key: "ImagenMapa", value: mapInfo.imageUrl });
            params.push({ key: "ImagenAncho", value: mapInfo.width + "" });
            params.push({ key: "ImagenAlto", value: mapInfo.height + "" });
        }
        showLoading(true);
        makePOSTApiRequest("setmapaservicioreserva", params)
            .then(function (resp) {
            var _b;
            showLoading(false);
            var msg = (_b = resp.message) !== null && _b !== void 0 ? _b : "Guardado";
            alert(msg);
        })["catch"](function (error) {
            showLoading(false);
            console.log(error);
            console.error(error);
        });
    };
}
function makePOSTApiRequest(action, params, files) {
    var headers = new Headers();
    headers.append("Accept", "application/json");
    var formData = new FormData();
    params.forEach(function (item) {
        formData.append(item.key, item.value);
    });
    formData.append("action", action);
    formData.append("IDClub", CLUB_ID);
    formData.append("AppVersion", APP_VERSION);
    return makeGETTokenRequest()
        .then(function (resp) {
        var _a;
        var responses;
        showLoading(false);
        responses = resp.response;
        formData.append("TokenID", responses[0].Token);
        if (files) {
            formData.append("Archivo", files[0]);
        }
        return fetch(BASE_URL, { method: "POST", headers: headers, mode: "cors", cache: "default", body: formData })
            .then(function (response) {
            return response.json();
        })
            .then(function (jsonObj) {
            var serverResp = jsonObj;
            if (serverResp.message == undefined) {
                console.log(serverResp.message || "Cannot decode response");
            }
            return serverResp;
        });
    })["catch"](function (error) {
        showLoading(false);
        alert(error);
    });
}
function makeGETTokenRequest() {
    var headers = new Headers();
    headers.append("Accept", "application/json");
    var formData = new FormData();
    formData.append("action", 'gettoken');
    formData.append("IDClub", CLUB_ID);
    formData.append("AppVersion", APP_VERSION);
    formData.append("Usuario", USER);
    formData.append("Clave", PASS);
    return fetch(BASE_URL, { method: "POST", headers: headers, mode: "cors", cache: "default", body: formData })
        .then(function (response) {
        return response.json();
    })
        .then(function (jsonObj) {
        var serverResp = jsonObj;
        if (serverResp.response == undefined) {
            throw new Error(serverResp.message || "Cannot decode response");
        }
        return serverResp;
    });
}
function makeGETApiRequest(action, params) {
    var headers = new Headers();
    headers.append("Accept", "application/json");
    var getParams = new URLSearchParams();
    params.forEach(function (item) {
        getParams.append(item.key, item.value);
    });
    getParams.append("action", action);
    getParams.append("IDClub", CLUB_ID);
    getParams.append("AppVersion", APP_VERSION);
    console.log(getParams.toString());
    return makeGETTokenRequest()
        .then(function (resp) {
        var _a;
        var responses;
        showLoading(false);
        responses = resp.response;
        getParams.append("TokenID", responses[0].Token);
        var url = BASE_URL + "?" + getParams.toString();
        return fetch(url, { method: "GET", headers: headers, mode: "cors", cache: "default" })
            .then(function (response) {
            return response.json();
        })
            .then(function (jsonObj) {
            var serverResp = jsonObj;
            if (serverResp.response == undefined) {
                throw new Error(serverResp.message || "Cannot decode response");
            }
            return serverResp;
        });
    })["catch"](function (error) {
        showLoading(false);
        alert(error);
    });
}
