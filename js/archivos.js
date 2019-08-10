function agregarDatos(datos){
    d=datos.split('||');
    $('#idArchivo').val(d[0]);

    }
function agregarDatos2(datos){
        d=datos.split('||');
        $('#idUI').val(d[0]);
        $('#nombreUI').val(d[1]);
        $('#emailUI').val(d[2]);
        $('#passwordUI').val(d[3]);
        $('#activaciondUI').val(d[5]);

    }

// document.addEventListener("DOMContentLoaded", ()=>{
//     let form = document.getElementById('form_subir');
//     form.addEventListener("submit", function(event){
//         event.preventDefault();
//         subir_archivos(this);
//     });
// });

// function subir_archivos(form){
//     let barra_estado = form.children[4].children[0],
//         span=barra_estado.children[0],
//         boton_cancelar=form.children[5].children[0].children[1];

//     barra_estado.classList.remove('barra_verde','barra_roja');

//     //peticion
//     let peticion = new XMLHttpRequest;

//     //progreso
//     peticion.upload.addEventListener("progress", (event) => {
//         let porcentaje = Math.round((event.loaded / event.total)*100);

//         console.log(porcentaje);
//         barra_estado.style.width = porcentaje+'%';
//         span.innerHTML= porcentaje+'%';
//     });

//     //finalizado
//     peticion.addEventListener("load", () =>{
//         barra_estado.classList.add('barra_verde');
//         span.innerHTML="Proceso Completado";
//     });

//     //enviar datos
//     peticion.open('post','subir.php');
//     peticion.send(new FormData(form));

//     //cancelar
//     boton_cancelar.addEventListener("click", ()=>{
//         peticion.abort();
//         barra_estado.classList.remove('barra_verde');
//         barra_estado.classList.add('barra_roja');
//         span.innerHTML='Proceso Cancelado';
//     });
// }