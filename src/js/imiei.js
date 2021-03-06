
function ini_datatable( qtabla, qphp, plongi,ordencolu ) {
/* inicializa datatable - textos en español y demas
  qtable es #id_del_tag_table
  qphp el nombre del script php donde se "dibuja" la tabla
  plongi es la longitud de pagina inicial
	si plongi negativo, ordering=false; el valor absoluto va a plongi
  ordencolu columna para orden inicial, hasta 2 columnas si es entero una columna, si tiene punto los decimales son la segunda columna
  reemplazo "ajax" por "sAjaxSource"
*/


var uc=$(qtabla+' thead th').length-1; /* ultima columna (en general, "acción")no se usa para ordenar */
var selected = "";
var orden = true;
if (plongi<0) {
	orden= false;
	plongi=Math.abs(plongi);
}
if (Number.isInteger(ordencolu)){
	/* solo una columna */
	arreorden =[[ ordencolu, 'asc' ]];
	}
	else{
		ocol1 = Math.floor(ordencolu);
		ocol2 = Math.floor((ordencolu-ocol1)*100);
		arreorden = [[ocol1,  'asc' ], [ocol2,  'asc' ]];
	}

var tablin = $(qtabla).dataTable( {
 "sAjaxSource": qphp,
 stateSave: true,
 "stateDuration": -1,
 "Processing": true,
 "ordering": orden,
 "order": arreorden,
 "columnDefs": [
			{"orderable": false, "targets": uc }
  ], 
 "serverSide": true,
 "pageLength": plongi,
 "lengthMenu": [ 5,10,15, 30, 60, 80, 100 ],
 "pagingType": "full_numbers",
 "language": {
     "emptyTable": "No hay datos disponibles en la tabla",
     "lengthMenu": "Mostrar _MENU_ registros",
	 "info": "L&iacute;neas _START_ a _END_ de _TOTAL_ ",
	 "infoFiltered":   "(filtrado de _MAX_ registros totales)",
	 "infoEmpty":      "L&iacute;nea 0 de 0",
	 "loadingRecords": "Cargando...",
     "processing":    "Procesando...",
     "search":         "Buscar:",
     "zeroRecords":    "No se encuentran registros",
     "paginate": {
        "first":      "|<<",
        "last":       ">>|",
        "next":       ">",
        "previous":   "<"
        },	 
     "aria": {
        "sortAscending":  ": activar para ordenar la columna ascendente",
        "sortDescending": ": activar para ordenar la columna descendente"
        }	
	 }

} );
     // aplico busque con ENTER
	$('#tablax_filter input').unbind();
	$('#tablax_filter input').bind('keyup', function(e) {
		if(e.keyCode == 13) {
		tablin.fnFilter(this.value);
	}
	}); 
 
 
 
} ;

function mayus(el){
	var sPos = el.selectionStart;		//para mantener la posicion del puntero...
	
	el.value=el.value.toUpperCase();
	
	el.selectionStart=sPos;
	el.selectionEnd=sPos;
}

function minus(el){
	var sPos = el.selectionStart;		//para mantener la posicion del puntero...
	
	el.value=el.value.toLowerCase();
	
	el.selectionStart=sPos;
	el.selectionEnd=sPos;
}

function tdecim(el,nd){
    if (el.value.length>0 && !isNaN(el.value)){
		el.value=Number(el.value).toFixed(nd);
	}
}


function setSexoStatus(cat,llama) {
	/* pare recuento....*/
	/*"cat" es "categoria", y tiene el sexo en la descripcion de la opcion; por ejemplo: */
	/*<option value="PRAD">PRIMIPARA-ADULTA   (HEMBRA)</option>*/
	/*establece campos "sexo" y "status" (que deben estar en el formulario!!), según la categoria*/

	var ix = cat.selectedIndex;
	var op=cat.options[ix].text;
    var psex=document.getElementById("sexo");
    var psta=document.getElementById("status");
    if (cat.value.length==0){
        psex.value='';
		if (psta!==null) {
			psta.value='';
		}
		return;
	}
	
	
	/*establezco sexo según la selección de categoria*/
	var pm=op.indexOf("MACHO");
	var ph=op.indexOf("HEMBRA");
	//var psex=document.getElementById("sexo");
	//var psta=document.getElementById("status");
	psex.options[0].disabled=true; 
	if (pm>0) {
		psex.options[1].disabled=true;     /*HEMBRA*/
		psex.options[2].disabled=false;    /*MACHO*/
		psex.options[3].disabled=true; 	   /*NODET*/
		psex.value='MACHO';
		}else{
		if (ph>0) {
			psex.options[1].disabled=false;    /*HEMBRA*/
			psex.options[2].disabled=true;     /*MACHO*/
			psex.options[3].disabled=true; 	   /*NODET*/
			psex.value='HEMBRA';
			}else{
				psex.options[1].disabled=false;     /*HEMBRA*/
				psex.options[2].disabled=false;     /*MACHO*/
				psex.options[3].disabled=false;     /*NODET*/
				if (llama=='change'){
					psex.value='';
					}					
			  }
		}

	
	/*establezco seleccion de status */
	if (psta===null) {
		/* censo MUDA, sin status */
		return
	}
	psta.options[0].disabled=true;  
	var lo = psta.options.length;
	if (pm>0) {
		for (i = 1; i < lo-1; i++) {
			psta.options[i].disabled=false;
			}
		psta.options[lo-1].disabled=true;  /*no corresponde*/
		if (llama=='change'){
			psta.value="";
		   }
		}else{
		if (ph>0) {
			for (i = 1; i < lo-1; i++) {
				psta.options[i].disabled=true;
				}
			psta.options[lo-1].disabled=false;  /*no corresponde*/
			psta.value='no corresponde';
			}else{
				for (i = 1; i < lo-1; i++) {
					psta.options[i].disabled=true;
					}
				psta.options[lo-1].disabled=false; /*no corresponde*/
				psta.value='no corresponde';					
			  }
		}
}



/* para ventanas MODAL */
function ventanaM(qhref,tit){
	    tituloM.innerText=tit;
		var qframe = document.getElementById("queva");
		if(tit=='ampli1200') {
			tituloM.innerText="";
			$("#quemodalito").width(1200);
			qframe.style.width="1180px";
		}
		qframe.src = qhref;
		$("#vModal").modal({backdrop: "static",keyboard: false});
	}
function cierroM(qref){
        $("#vModal").modal("hide");
        location.href = qref;
    }
function cierroMNO(){
        $("#vModal").modal("hide");
    }	
	
function ventanaMini(qhref,tit){
	    if (tit!=""){
			titux.innerText=tit;
		}
		var qframe = document.getElementById("quevax");
		qframe.src = qhref;
		$("#vModalx").modal({backdrop: "static",keyboard: false});
	}
function cierroMini(qref){
        $("#vModalx").modal("hide");
        location.href = qref;
    }	
function cierroMiniNO(){
        $("#vModalx").modal("hide");
    }	
function cierroMiniSI(qref){
        $("#vModalx").modal("hide");
		parent.cierroM(qref);
    }

function ventanaMIns(qhref,tit){
	    tituloM.innerText=tit;
		var qframe = document.getElementById("queva");
		qframe.src = qhref;
		$("#vModalIns").modal({backdrop: "static",keyboard: false});
	}	

//function ventanaMap(qhref,geo){
//    var patt = new RegExp(/^POINT\(-?\d{1,3}(\.(\d+)?)?_-?\d{1,3}(\.(\d+)?)?\)/);
//    if(patt.test(geo)==true){
function ventanaMap(qhref){
	//alert(qhref);
			var qframe = document.getElementById("queva");
			qframe.src = qhref;
				$("#quemodalito").width(680);
				$("#quemodalito").height(520);
				qframe.style.width="660px";
				qframe.style.height="500px";
			
			$("#vModalMap").modal({backdrop: "static",keyboard: false});
	}
//}	
	
	
/* para resultados */
function datosMres(pregunta,val){	
	    Pregunta_tit.value = document.getElementById("P"+pregunta).innerHTML;
	    Pregunta_cal.value = pregunta;
		if (val!="") {
			numero.value = val;
		}
		document.getElementById("fRespuestas").submit();
	}

function ventanaMres(pregunta){
		tituloM.innerText=pregunta;		
		$("#vModal").modal({backdrop: "static",keyboard: true});
	}


	
/* para miSideBar */

function MiopenNav() {
    document.getElementById("miSidenav").style.width = "300px";
}

function MicloseNav() {
    document.getElementById("miSidenav").style.width = "0";
}

/* agregar :00 si la longitud del string hora corresponde a HH:mm */
function aSegundos (v){
	if (v.value.length==5) {
		v.value=v.value+":00"
	}
}




/* observaciones terrestres */
function setAplicacionDrogaPartida(eta) {
	/* para anestesia....*/
	/*"eta" es "es la etapa de registro de anestesia ("tipo" en la tabla); según su valor */
	/*establece campos "aplicacion", "droga" y "status_ml" (que deben estar en el formulario!!)*/
	var ix = eta.selectedIndex;
	var op=eta.options[ix].text;
    var papl=document.getElementById("aplicacion");
    var pdro=document.getElementById("droga");
    var pdml=document.getElementById("droga_ml");
    var ppar=document.getElementById("partida");
    if (eta.value.length==0){
		papl.value='';
		pdro.value='';
		pdml.value='';
		ppar.value='';
		}else{
			/*establezco accessos según la selección*/
			papl.options[0].disabled=true; 
			pdro.options[0].disabled=true; 
    		if (  (op=="COMENTA") || (op=="INDUCCION") || (op.indexOf("FIN")>=0) ) {
				papl.options[1].disabled=false; 
				papl.options[2].disabled=true; 
				papl.options[3].disabled=true; 
				pdro.options[1].disabled=false; 
				pdro.options[2].disabled=true; 
				pdro.options[3].disabled=true; 
				pdro.options[4].disabled=true; 
				pdro.options[5].disabled=true; 
				pdml.disabled=true;
				pdml.required=false;
				ppar.disabled=true;
	    		papl.value='no corresponde';
	    		pdro.value='no corresponde';
	    		pdml.value='';
	    		ppar.value='';
		     }else{
	    		if (papl.value=='no corresponde') {
					papl.value="";
				}
	    		if (pdro.value=='no corresponde') {
					pdro.value="";
				}				
				papl.options[1].disabled=true; 
				papl.options[2].disabled=false; 
				papl.options[3].disabled=false; 
				pdro.options[1].disabled=true; 
				pdro.options[2].disabled=false; 
				pdro.options[3].disabled=false; 
				pdro.options[4].disabled=false; 
				pdro.options[5].disabled=false; 
				pdml.disabled=false;
				pdml.required=true;
				ppar.disabled=false;
	    		/*pdml.value='';
	    		ppar.value='';*/
	              }
	        }
}

/* */
function irAtras() {
    window.history.back();
}


/* PARA validator.js */
/* funcion validacion enteros */
function validatorEntero ($el){
	if ($el.val().length>0){
		if (isNaN($el.val())) {
		   return false;		/* no es un numero */
		   }
		var n = Number($el.val());
		var dnum = Math.trunc(n);
		if (n != dnum) {
			return false;		/* era con punto decimal */
		}
		var dmin = Number($el.data("dmin"));
		var dmax = Number($el.data("dmax"));
		if ((dmin>dnum) || (dnum>dmax)) {
			 return false;		/* fuera de rango */
			 }
	}
	return true
}


/* PARA validator.js */
/* funcion validacion decimales */
function validatorDecimal ($el){	
	if ($el.val().length>0){
		if (isNaN($el.val())) {
		   return false;		/* no es un numero */
		   }
		var dnum = Number($el.val());
		var dmin = Number($el.data("dmin"));
		var dmax = Number($el.data("dmax"));
		if ((dmin>dnum) || (dnum>dmax)) {
			 return false;		/* fuera de rango */
			 }
    }
	return true;
}
/* cambia espacios por underscore */
function punLL(ll){
   var geoLL=document.getElementById(ll).value;
   var geoR=geoLL.replace(/ /g,"_");
   return geoR;
}  