// import { fabric } from 'https://cdn.jsdelivr.net/npm/fabric';

// const BASE_URL = "https://www.miclubapp.com/services/club.php";
// const BASE_URL = "http://192.168.5.110:8080/services";
const BASE_URL = "https://appdev.miclubapp.com/services/club.php";
const CLUB_ID = "8";
const APP_VERSION = "33";
const TOKENID = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczpcL1wvd3d3Lm1pY2x1YmFwcC5jb20iLCJhdWQiOiJodHRwczpcL1wvd3d3Lm1pY2x1YmFwcC5jb20iLCJpYXQiOjE2Nzk1ODg0MzYsIm5iZiI6MTY3OTU4ODQzNiwiZXhwIjoxNjc5NjQ4NDM2LCJkYXRhIjp7IklEVXN1YXJpb1dTIjoiMjQiLCJOb21icmUiOiJUb2tlbkdlbmVyaWNvIiwiRW1wcmVzYSI6IlRPRE9TIn19.gHsdXNtlf0jtdFGXDVAd3NwKeQCk45qPgYG6A_ZkaGg";

interface ActionShapePosition {
    IDElemento: string;
    PosicionX: number;
    PosicionY: number;
    Ancho: number;
    Alto: number;
}

interface ActionShape {
    element: ClubReservationElement;
    group: fabric.Group;
}

interface MapImageConfiguration {
    imageUrl: string;
    width: number;
    height: number;
    mapImage?: fabric.Image;
}

interface ClubSavedMapConfiguration {
    ImagenMapa?: string,
    ImagenAncho: number
    ImagenAlto?: number,
    Elementos?: Array<ActionShapePosition>
}

class ClubReservationMapConfiguration {
    allShapes: Array<ActionShape>;
    service?: ClubReservationService | null;
    mapConfig?: MapImageConfiguration | null;

    constructor() {
        this.allShapes = [];
        this.service = null;
        this.mapConfig = null;
    }

    removeShape(shape: ActionShape) {
        //Remove the shape from the canvas
        for (let i: number = 0; i < this.allShapes.length; i++) {
            const existingShape = this.allShapes[i];
            if( shape.element.IDElemento == existingShape.element.IDElemento ) {
                canvas.remove(existingShape.group);
                this.allShapes.splice(i, 1);
                break;
            }
        }
    }
    
    removeAllShapes() {
        //Remove the shapes from the canvas
        for (let i: number = 0; i < currentConfiguration.allShapes.length; i++) {
            const shape = currentConfiguration.allShapes[i];
            canvas.remove(shape.group);
        }
        //Clear allShapes array
        currentConfiguration.allShapes = Array<ActionShape>();
    }
}

interface ServerResponse<T> {
    success: boolean;
    message?: string;
    response?: T
}

interface ClubReservationService {
    IDServicio: string;
    Nombre: string;
    Icono?: string;
}

interface ClubReservationElement {
    IDElemento: string;
    Nombre?: string;
}

interface ClubImageUploadResponse {
    path?: string;
    url?: string;
}

const canvas = new fabric.Canvas("canvas-root", {
    backgroundColor: 'rgb(0,80,150)',
    selectionColor: 'rgba(0,0,200, 1.0)',
    selectionLineWidth: 2
});


const currentConfiguration = new ClubReservationMapConfiguration();
const loaderElement = document.getElementById("loader");
let allServices = Array<ClubReservationService>();

let initialViewPortTransfor = canvas.viewportTransform;
const zoomLabel = document.getElementById("zoom-level");
updateZoomLabel();
const mapImageSizeLabel = document.getElementById("map-image-size");
updateImageSizeLabel();

function updateZoomLabel() {
    if(zoomLabel !== null ) {
        let zoom = canvas.getZoom();
        zoomLabel.textContent = `Zoom (${Math.round(zoom * 100)}%)`;
    }
}

function updateImageSizeLabel() {
    if(mapImageSizeLabel !== null) {
        const width = currentConfiguration?.mapConfig?.mapImage?.width ?? 0;
        const height = currentConfiguration?.mapConfig?.mapImage?.height ?? 0;
        mapImageSizeLabel.textContent = `Tamaño mapa: [Ancho: ${width} - Alto: ${height}]`;
    }
}

canvas.on('mouse:wheel', (opt) => {
    let delta = opt.e.deltaY;
    let zoom = canvas.getZoom();
    zoom *= 0.999 ** delta;
    if (zoom > 20) zoom = 20;
    if (zoom < 0.01) zoom = 0.01;
    canvas.zoomToPoint({ x: opt.e.offsetX, y: opt.e.offsetY }, zoom);
    opt.e.preventDefault();
    opt.e.stopPropagation();
    updateZoomLabel();
});

canvas.on('mouse:down', function (opt) {
    let evt = opt.e;
    if (evt.altKey === true) {
        this.isDragging = true;
        this.selection = false;
        this.lastPosX = evt.clientX;
        this.lastPosY = evt.clientY;
    }
});

canvas.on('mouse:move', (opt) => {
    if (canvas.isDragging) {
        let e = opt.e;
        let vpt = canvas.viewportTransform;
        vpt[4] += e.clientX - canvas.lastPosX;
        vpt[5] += e.clientY - canvas.lastPosY;
        canvas.requestRenderAll();
        canvas.lastPosX = e.clientX;
        canvas.lastPosY = e.clientY;
    }
});

canvas.on('mouse:up', (opt) => {
    // on mouse up we want to recalculate new interaction
    // for all objects, so we call setViewportTransform
    canvas.setViewportTransform(canvas.viewportTransform);
    canvas.isDragging = false;
    canvas.selection = true;
});

function showLoading(show: boolean) {
    if (loaderElement !== null) {
        loaderElement.style.display = show ? "block" : "none";
    }
}

function addElementShape(elemet: ClubReservationElement, left?: number | null, top?: number | null, width?: number | null, height?: number | null) {
    const index = currentConfiguration.allShapes.length;
    const rectWidth = width ?? 40;
    const rectHeight = height ?? 40;
    const rect = new fabric.Rect({
        fill: "rgba(0,200,0, 0.5)",
        originX: 'center',
        originY: 'center',
        width: rectWidth,
        height: rectHeight
    });
    const text = new fabric.Textbox(elemet.Nombre ?? elemet.IDElemento, {
        fontSize: 10,
        originX: 'center',
        originY: 'center',
        width: rectWidth,
        height: rectHeight,
        textAlign: 'center'
    });

    let leftIndex = index;
    let fixedLeft = leftIndex * (rectWidth + 10);
    let topIndex = Math.trunc(fixedLeft / canvas.width);
    let fixedTop = topIndex * (rectWidth + 10);
    fixedLeft = (fixedLeft - (topIndex * canvas.width));

    const group = new fabric.Group([rect, text], {
        top: top ?? fixedTop,
        left: left ?? fixedLeft
    });

    currentConfiguration.allShapes.push({ element: elemet, group: group });
    rect.set('selectable', true);
    canvas.add(group);
}

//Configure the remove all shapes button
const removeAllShapesButton = document.getElementById("remove-all-button");
if (removeAllShapesButton !== null) {
    removeAllShapesButton.onclick = () => {
        currentConfiguration.removeAllShapes();
    };
}

//Configure select service
const serviceSelector = document.getElementById("service-selector") as HTMLSelectElement;
if (serviceSelector !== null) {
    //try to load the services
    showLoading(true);
    makeGETApiRequest<[ClubReservationService]>("getservicios", [])
        .then((serverResp) => {
            showLoading(false);
            allServices = serverResp.response ?? [];
            let isFirstElement = true;
            serverResp.response?.map((value) => {
                const newElement = document.createElement("option");
                newElement.setAttribute("value", value.IDServicio);
                const node = document.createTextNode(value.Nombre);
                newElement.appendChild(node);
                serviceSelector.appendChild(newElement);

                //Load the first element because it is selected by default
                if (isFirstElement) {
                    isFirstElement = false;
                    requestAndPopulateElements(value, true);
                }
            });
        })
        .catch((error) => {
            showLoading(false);
            alert(error.message);
        });
    serviceSelector.onchange = () => {
        if(!confirm("¿Está seguro de cambiar el servicio a configurar?\nTodos los cambios no guardados se perderán")) {
            serviceSelector.value = currentConfiguration.service?.IDServicio ?? "";
            return;
        }
        const serviceId = serviceSelector.value
        //Each time the services changes, populate again the canvas with the elements
        const service = allServices.find(element => element.IDServicio == serviceId);
        if (service) {
            requestAndPopulateElements(service, true);
        }
    };
}

function requestAndPopulateElements(service: ClubReservationService, loadingMap: boolean) {
    //First remove all shapes
    currentConfiguration.removeAllShapes();
    //Request the elements by the service
    showLoading(true);
    currentConfiguration.service = service;
    makeGETApiRequest<[ClubReservationElement]>("getelementos", [{ key: "IDServicio", value: service.IDServicio }])
        .then((response) => {
            showLoading(false);
            const numResponses = response.response?.length ?? 0;
            if (!response.response?.length) {
                alert(`No hay elementos configurados en el servicio de ${service.Nombre}, configure primero los elementos`);
            }
            else {
                //Populate the elements
                for (const element of response.response ?? []) {
                    addElementShape(element);
                }
            }
            if( loadingMap ) {
                requestPreviousMap();
            }
        })
        .catch((error) => {
            showLoading(false);
            alert(error.message);
        });
}

//Configure map image input
const mapImageInput = document.getElementById("map-image-input") as HTMLInputElement;
const mapImageUploadButton = document.getElementById("map-image-upload") as HTMLButtonElement;
if (mapImageInput !== null && mapImageUploadButton !== null) {
    mapImageUploadButton.onclick = () => {
        if(mapImageInput.files === null) {
            return;
        }
        if(!mapImageInput.files.length) {
            alert("Seleccione un archivo par ala imagen del mapa");
            return;
        }
        const serviceId = currentConfiguration.service?.IDServicio;
        if(!serviceId) {
            alert("Primero seleccione un servicio");
            return;
        }
        showLoading(true);
        makePOSTApiRequest<ClubImageUploadResponse>("setimagenmapaservicioreserva", [{ key: "IDServicio", value: serviceId }], mapImageInput.files)
            .then((response) => {
                let imgUrl = response.response?.url;
                if (imgUrl) {
                    changeMapImage(imgUrl);
                }
            })
            .catch((error) => {
                alert(error);
                console.error("Error uploading file: " + error);
            })
    };
}

function changeMapImage(imageUrl?: string) {
    //Remove previous background image
    const mapImage = currentConfiguration.mapConfig?.mapImage;
    if (mapImage) {
        canvas.remove(mapImage);
        currentConfiguration.mapConfig = null;
    }
    if(!imageUrl) {
        updateImageSizeLabel();
        return;
    }

    fabric.Image.fromURL(imageUrl, (oImg: fabric.Image) => {
        currentConfiguration.mapConfig = {
            mapImage: oImg, 
            width: oImg.width, 
            height: oImg.height, 
            imageUrl: imageUrl
        };
        canvas.add(oImg);
        let scaleWidthFactor = canvas.width / oImg.width;
        let scaleHeightFactor = canvas.height / oImg.height;
        let scaleFactor = Math.min(scaleWidthFactor, scaleHeightFactor);
        // oImg.scale(scaleFactor);
        oImg.set('selectable', false);
        if(initialViewPortTransfor) {
            canvas.setViewportTransform(initialViewPortTransfor);
        }
        canvas.zoomToPoint({ x: 0, y: 0 }, scaleFactor);
        canvas.sendToBack(oImg);
        updateZoomLabel();
        updateImageSizeLabel();
    });
}

function loadMapInCurrentConfiguration(shapes: Array<ActionShapePosition>, mapInfo?: MapImageConfiguration | null) {
    canvas.discardActiveObject();
    //Use a copy of the shapes to avoid issues when inserting new elements
    const copyShapes = currentConfiguration.allShapes.map(value => value);
    //Try to load the position and size for each existing shape
    for(const existingShape of copyShapes) {
        const newShape = JSON.parse(shapes).find((shape) => {
            if( shape.IDElemento == existingShape.element.IDElemento ) {
                return shape;
            }
        })
        if(newShape) {
            //Remove the preivous one and create a new one
            currentConfiguration.removeShape(existingShape);
            //Create a new shape
            addElementShape(existingShape.element, newShape.PosicionX, newShape.PosicionY, newShape.Ancho, newShape.Alto);
        }
    }
    canvas.requestRenderAll();
    //Try to load map
    changeMapImage(mapInfo?.imageUrl);
}

function requestPreviousMap() {
    let service = currentConfiguration.service;
        if(!service) {
            alert("Primero debe seleccionar un servicio");
            return;
        }
        showLoading(true);
        makeGETApiRequest<ClubSavedMapConfiguration>("getmapaservicioreserva", [{key: "IDServicio", value: service.IDServicio}])
        .then((response) => {
            showLoading(false);
            const savedMap = response.response;
            const imageUrl = savedMap?.ImagenMapa;
            if(savedMap) {
                if(imageUrl ) {
                    loadMapInCurrentConfiguration(savedMap.Elementos ?? [], {
                        imageUrl: imageUrl, 
                        width: savedMap.ImagenAncho ?? 0, 
                        height: savedMap.ImagenAlto ?? 0
                    });
                }
                else {
                    loadMapInCurrentConfiguration(savedMap.Elementos ?? [], null);
                }
            }
        })
        .catch((error) => {
            showLoading(false);
            alert(error);
            console.error(error);
        });
}

//Configure load map
const loadMapButton = document.getElementById("load-map") as HTMLButtonElement;
if( loadMapButton !== null ){
    loadMapButton.onclick = () => {
        requestPreviousMap();
    };
}

//Configure save map
const saveMapButton = document.getElementById("save-map") as HTMLButtonElement;
if( saveMapButton !== null ) {
    saveMapButton.onclick = () => {
        let params: [{key: string, value: string}] = [{key: "IDServicio", value: currentConfiguration.service?.IDServicio ?? ""}];
        const elementsList = currentConfiguration.allShapes.map((shape) => {
            return {
                "IDElemento": shape.element.IDElemento,
                "PosicionX": shape.group.left,
                "PosicionY": shape.group.top,
                "Ancho": shape.group.width * shape.group.scaleX,
                "Alto": shape.group.height * shape.group.scaleY,
            };
        });
        params.push({key: "Elementos", value: JSON.stringify(elementsList)})
        const mapInfo = currentConfiguration.mapConfig;
        if( mapInfo ) {
            params.push({key: "ImagenMapa", value: mapInfo.imageUrl})
            params.push({key: "ImagenAncho", value: mapInfo.width+""})
            params.push({key: "ImagenAlto", value: mapInfo.height+""})
        }
        showLoading(true);
        makePOSTApiRequest<string>("setmapaservicioreserva", params)
        .then((resp) => {
            showLoading(false);
            let msg = resp.message ?? "Guardado";
            alert(msg);
        })
        .catch((error) => {
            showLoading(false);
            alert(error);
            console.error(error);
        })
    };
}

function makePOSTApiRequest<T>(action: string, params: [{ key: string, value: string }] | [], files?: FileList | null): Promise<ServerResponse<T>> {
    const headers = new Headers();
    headers.append("Accept", "application/json");
    const formData = new FormData();
    params.forEach((item) => {
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
        .then((response) => {
            return response.json();
        })
        .then((jsonObj) => {
            const serverResp = jsonObj as ServerResponse<T>;
            if (serverResp.response == undefined) {
                throw new Error(serverResp.message || "Cannot decode response");
            }
            return serverResp;
        });
}

function makeGETApiRequest<T>(action: string, params: [{ key: string, value: string }] | []): Promise<ServerResponse<T>> {
    const headers = new Headers();
    headers.append("Accept", "application/json");
    const getParams = new URLSearchParams();
    params.forEach((item) => {
        getParams.append(item.key, item.value);
    });
    getParams.append("action", action);
    getParams.append("IDClub", CLUB_ID);
    getParams.append("AppVersion", APP_VERSION);
    getParams.append("TokenID", TOKENID);
    const url = BASE_URL + "?" + getParams.toString();
    return fetch(url, { method: "GET", headers: headers, mode: "cors", cache: "default" })
        .then((response) => {
            return response.json();
        })
        .then((jsonObj) => {
            const serverResp = jsonObj as ServerResponse<T>;
            if (serverResp.response == undefined) {
                throw new Error(serverResp.message || "Cannot decode response");
            }
            return serverResp;
        });
}