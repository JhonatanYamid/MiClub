// import { fabric } from 'https://cdn.jsdelivr.net/npm/fabric';
// const BASE_URL = "https://www.miclubapp.com/services/club.php";
// const BASE_URL = "http://192.168.5.110:8080/services";
var BASE_URL = "https://appdev.miclubapp.com/services/club.php";
var CLUB_ID = "8";
var APP_VERSION = "33";
var TOKENID = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczpcL1wvd3d3Lm1pY2x1YmFwcC5jb20iLCJhdWQiOiJodHRwczpcL1wvd3d3Lm1pY2x1YmFwcC5jb20iLCJpYXQiOjE2Nzk3ODEzOTEsIm5iZiI6MTY3OTc4MTM5MSwiZXhwIjoxNjc5ODQxMzkxLCJkYXRhIjp7IklEVXN1YXJpb1dTIjoiMjQiLCJOb21icmUiOiJUb2tlbkdlbmVyaWNvIiwiRW1wcmVzYSI6IlRPRE9TIn19.ufTaPyCsY-CqkVAzBZv2z3JG59j395AH1eXh1D8-wZA";
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
    var _a, _b, _c, _d, _e, _f;
    if (mapImageSizeLabel !== null) {
        var width = (_c = (_b = (_a = currentConfiguration === null || currentConfiguration === void 0 ? void 0 : currentConfiguration.mapConfig) === null || _a === void 0 ? void 0 : _a.mapImage) === null || _b === void 0 ? void 0 : _b.width) !== null && _c !== void 0 ? _c : 0;
        var height = (_f = (_e = (_d = currentConfiguration === null || currentConfiguration === void 0 ? void 0 : currentConfiguration.mapConfig) === null || _d === void 0 ? void 0 : _d.mapImage) === null || _e === void 0 ? void 0 : _e.height) !== null && _f !== void 0 ? _f : 0;
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
    var _a;
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
    var text = new fabric.Textbox((_a = elemet.Nombre) !== null && _a !== void 0 ? _a : elemet.IDElemento, {
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
    makeGETApiRequest("getservicios", [])
        .then(function (serverResp) {
        var _a, _b;
        showLoading(false);
        allServices = (_a = serverResp.response) !== null && _a !== void 0 ? _a : [];
        var isFirstElement = true;
        (_b = serverResp.response) === null || _b === void 0 ? void 0 : _b.map(function (value) {
            var newElement = document.createElement("option");
            newElement.setAttribute("value", value.IDServicio);
            var node = document.createTextNode(value.Nombre);
            newElement.appendChild(node);
            serviceSelector.appendChild(newElement);
            //Load the first element because it is selected by default
            if (isFirstElement) {
                isFirstElement = false;
                requestAndPopulateElements(value, true);
            }
        });
    })["catch"](function (error) {
        showLoading(false);
        alert(error.message);
    });
    serviceSelector.onchange = function () {
        var _a, _b;
        if (!confirm("¿Está seguro de cambiar el servicio a configurar?\nTodos los cambios no guardados se perderán")) {
            serviceSelector.value = (_b = (_a = currentConfiguration.service) === null || _a === void 0 ? void 0 : _a.IDServicio) !== null && _b !== void 0 ? _b : "";
            return;
        }
        var serviceId = serviceSelector.value;
        //Each time the services changes, populate again the canvas with the elements
        var service = allServices.find(function (element) { return element.IDServicio == serviceId; });
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
        var _a, _b, _c, _d;
        showLoading(false);
        var numResponses = (_b = (_a = response.response) === null || _a === void 0 ? void 0 : _a.length) !== null && _b !== void 0 ? _b : 0;
        if (!((_c = response.response) === null || _c === void 0 ? void 0 : _c.length)) {
            alert("No hay elementos configurados en el servicio de ".concat(service.Nombre, ", configure primero los elementos"));
        }
        else {
            //Populate the elements
            for (var _i = 0, _e = (_d = response.response) !== null && _d !== void 0 ? _d : []; _i < _e.length; _i++) {
                var element = _e[_i];
                addElementShape(element);
            }
        }
        if (loadingMap) {
            requestPreviousMap();
        }
    })["catch"](function (error) {
        showLoading(false);
        alert(error.message);
    });
}
//Configure map image input
var mapImageInput = document.getElementById("map-image-input");
var mapImageUploadButton = document.getElementById("map-image-upload");
if (mapImageInput !== null && mapImageUploadButton !== null) {
    mapImageUploadButton.onclick = function () {
        var _a;
        if (mapImageInput.files === null) {
            return;
        }
        if (!mapImageInput.files.length) {
            alert("Seleccione un archivo par ala imagen del mapa");
            return;
        }
        var serviceId = (_a = currentConfiguration.service) === null || _a === void 0 ? void 0 : _a.IDServicio;
        if (!serviceId) {
            alert("Primero seleccione un servicio");
            return;
        }
        showLoading(true);
        makePOSTApiRequest("setimagenmapaservicioreserva", [{ key: "IDServicio", value: serviceId }], mapImageInput.files)
            .then(function (response) {
            var _a;
            var imgUrl = (_a = response.response) === null || _a === void 0 ? void 0 : _a.url;
            if (imgUrl) {
                changeMapImage(imgUrl);
            }
        })["catch"](function (error) {
            alert(error);
            console.error("Error uploading file: " + error);
        });
    };
}
function changeMapImage(imageUrl) {
    var _a;
    //Remove previous background image
    var mapImage = (_a = currentConfiguration.mapConfig) === null || _a === void 0 ? void 0 : _a.mapImage;
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
        var _a, _b, _c, _d;
        showLoading(false);
        var savedMap = response.response;
        var imageUrl = savedMap === null || savedMap === void 0 ? void 0 : savedMap.ImagenMapa;
        if (savedMap) {
            if (imageUrl) {
                loadMapInCurrentConfiguration((_a = savedMap.Elementos) !== null && _a !== void 0 ? _a : [], {
                    imageUrl: imageUrl,
                    width: (_b = savedMap.ImagenAncho) !== null && _b !== void 0 ? _b : 0,
                    height: (_c = savedMap.ImagenAlto) !== null && _c !== void 0 ? _c : 0
                });
            }
            else {
                loadMapInCurrentConfiguration((_d = savedMap.Elementos) !== null && _d !== void 0 ? _d : [], null);
            }
        }
    })["catch"](function (error) {
        showLoading(false);
        alert(error);
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
        var _a, _b;
        var params = [{ key: "IDServicio", value: (_b = (_a = currentConfiguration.service) === null || _a === void 0 ? void 0 : _a.IDServicio) !== null && _b !== void 0 ? _b : "" }];
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
            var _a;
            showLoading(false);
            var msg = (_a = resp.message) !== null && _a !== void 0 ? _a : "Guardado";
            alert(msg);
        })["catch"](function (error) {
            showLoading(false);
            alert(error);
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
    formData.append("TokenID", TOKENID);
    if (files) {
        formData.append("Archivo", files[0]);
    }
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
    getParams.append("TokenID", TOKENID);
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
}
