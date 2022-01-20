
function ini_datatable( qtabla, qphp, plongi ) {
/* inicializa datatable - textos en españos y demas
qtable es #id_del_tag_table
qphp el nombre del script php donde se "dibuja" la tabla
plongi es la longitud de pagina inicial
reemplazo "ajax" por "sAjaxSource"
*/

$(qtabla).dataTable( {
 "sAjaxSource": qphp,
 stateSave: true,
 "stateDuration": -1,
 "Processing": true,
 "serverSide": true,
 "pageLength": plongi,
 "lengthMenu": [ 5,10, 20, 40, 80, 100 ],
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
 

} ;

function mayus(el){
	el.value=el.value.toUpperCase();
}

function minus(el){
	el.value=el.value.toLowerCase();
}

function minus(el){
	el.value=el.value.toLowerCase();
}

function tdecim(el,nd){
    if (el.value.length>0){
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
		psta.value='';
		}else{	
			/*establezco accessos según la selección*/
			var pm=op.indexOf("MACHO");
			var ph=op.indexOf("HEMBRA");
			var psex=document.getElementById("sexo");
			var psta=document.getElementById("status");
			psex.options[0].disabled=true; 
			psta.options[0].disabled=true;  
			var lo = psta.options.length;
			if (pm>0) {
				/*psex.options[1].disabled=true; 
				psex.options[2].disabled=false; 
				psex.options[3].disabled=true; 				*/
				psex.disabled=true; 
				psex.value='MACHO';
				/*for (i = 1; i < lo-1; i++) {
					psta.options[i].disabled=false;
				    }*/
				psta.disabled=false;
				psta.options[lo-1].disabled=true;
				if (llama=='change'){
					psta.value="";
				   }
				}else{
				if (ph>0) {
					/*psex.options[1].disabled=false; 
					psex.options[2].disabled=true; 
					psex.options[3].disabled=true;*/
					psex.disabled=true; 
					psex.value='HEMBRA'
					/*for (i = 1; i < lo-1; i++) {
						psta.options[i].disabled=true;
						}
					psta.options[lo-1].disabled=false;*/
					psta.disabled=true;
					psta.value='no corresponde';

					}else{
						psex.disabled=false; 
						psta.disabled=true;
						/*psex.options[1].disabled=false; 
						psex.options[2].disabled=false; 
						psex.options[3].disabled=false;*/
						if (llama=='change'){
						    psex.value='';
							}
						/*for (i = 1; i < lo-1; i++) {
							psta.options[i].disabled=true;
							}*/
						psta.options[lo-1].disabled=false;
						psta.value='no corresponde';
						
					  }
				}
	}
}


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
    		if (  (op=="COMENTA") || (op.indexOf("FIN")>=0) ) {
				papl.options[1].disabled=false; 
				papl.options[2].disabled=true; 
				papl.options[3].disabled=true; 
				pdro.options[1].disabled=false; 
				pdro.options[2].disabled=true; 
				pdro.options[3].disabled=true; 
				pdro.options[4].disabled=true; 
				pdro.options[5].disabled=true; 
				pdml.disabled=true;
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
				ppar.disabled=false;
	    		/*pdml.value='';
	    		ppar.value='';*/
	              }
	        }
}

