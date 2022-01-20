/* para el sistema de ayuda */
/* cada página con ayuda (un signo de interrogación en la ventana)
   tiene su página correspondiente en el sistema de ayuda */
/* esta función busca en uebJelp (1er.columna) a uPagina y
   abre la página de ayuda (2a.columna) en nueva ventana o solapa */   


function ayudote(uPagina) {
/* muestra la pagina de ayuda que le corresponde a la pagina uPagina */
	uBarra= uPagina.lastIndexOf("/")+1;
	lPagina= uPagina.substr(uBarra,100);
	
	var uebJelp = [
	["censo_menu0.php","CensosEMS.html"],
	["censo_index_mensual.php","CensosMensuales9500Datos.html"],
	["censo_totales_mensuales.php","CensosMensuales9500Totales.html"],
	["pelos_de_valdes.php","censosPelosHembras.html"],
	["censista_borrar.php","censosRMcensitas.html"],
	["censista_crear.php","censosRMcensitas.html"],
	["censista_editar.php","censosRMcensitas.html"],
	["censista_index.php","censosRMcensitas.html"],
	["censo_totales_compara.php","CensosRMCompa.html"],
	["recuento_borrar.php","CensosRMComposicion.html"],
	["recuento_crear.php","CensosRMComposicion.html"],
	["recuento_editar.php","CensosRMComposicion.html"],
	["recuento_index.php","CensosRMComposicion.html"],
	["censo_borrar.php","CensosRMIcenso.html"],
	["censo_crear.php","CensosRMIcenso.html"],
	["censo_editar.php","CensosRMIcenso.html"],
	["censo_index.php","CensosRMIcenso.html"],
	["grupo_borrar.php","CensosRMIgrupo.html"],
	["grupo_crear.php","CensosRMIgrupo.html"],
	["grupo_editar.php","CensosRMIgrupo.html"],
	["grupo_index.php","CensosRMIgrupo.html"],
	["vw_censo_sector_index.php","CensosRMIsector.html"],
	["sector_borrar.php","CensosRMIsectorCaminado.html"],
	["sector_crear.php","CensosRMIsectorCaminado.html"],
	["sector_editar.php","CensosRMIsectorCaminado.html"],
	["sector_copiado_borrar.php","CensosRMIsectorCopiado.html"],
	["sector_copiado_crear.php","CensosRMIsectorCopiado.html"],
	["sector_copiado_editar.php","CensosRMIsectorCopiado.html"],
	["sector_copiado_grupo_index.php","CensosRMIsectorCopiado.html"],
	["censo_totales.php","CensosRMTotales.html"],
	["mdatos_menu0.php","MasDatos.html"],
	["categoria_borrar.php","MasDatosCategoriasEdad.html"],
	["categoria_crear.php","MasDatosCategoriasEdad.html"],
	["categoria_editar.php","MasDatosCategoriasEdad.html"],
	["categoria_index.php","MasDatosCategoriasEdad.html"],
	["colaborador_borrar.php","MasDatosColaboradores.html"],
	["colaborador_crear.php","MasDatosColaboradores.html"],
	["colaborador_editar.php","MasDatosColaboradores.html"],
	["colaborador_index.php","MasDatosColaboradores.html"],
	["instrumentos_borrar.php","MasDatosInstrumentos.html"],
	["instrumentos_crear.php","MasDatosInstrumentos.html"],
	["instrumentos_editar.php","MasDatosInstrumentos.html"],
	["instrumentos_index.php","MasDatosInstrumentos.html"],
	["playa_borrar.php","MasDatosPlayas.html"],
	["playa_crear.php","MasDatosPlayas.html"],
	["playa_editar.php","MasDatosPlayas.html"],
	["playa_index.php","MasDatosPlayas.html"],
	["vecindario_borrar.php","MasDatosVecindarios.html"],
	["vecindario_crear.php","MasDatosVecindarios.html"],
	["vecindario_editar.php","MasDatosVecindarios.html"],
	["vecindario_index.php","MasDatosVecindarios.html"],
	["oremota_menu0.php","ORemotaEMS.html"],
	["aBuceos_numeros.php","ORemotaEMSBuceosEsta.html"],
	["aBuceos_descarga.php","ORemotaEMSBuceosGrafi.html"],
	["aBuceos_grafico.php","ORemotaEMSBuceosGrafi.html"],
	["aLocalizaciones_numeros.php","ORemotaEMSLocalizaEsta.html"],
	["aLocalizacionesARGOS.php","ORemotaEMSLocalizaIngreso.html"],
	["anestesia_borrar.php","OTerrestreEMSRelevaFichaAnestesi.html"],
	["anestesia_crear.php","OTerrestreEMSRelevaFichaAnestesi.html"],
	["anestesia_editar.php","OTerrestreEMSRelevaFichaAnestesi.html"],
	["anestesia_index.php","OTerrestreEMSRelevaFichaAnestesi.html"],
	["aLocalizaciones_descarga.php","ORemotaEMSLocalizaMapa.html"],
	["aRemota_fichaViajes.php","ORemotaEMSViajesFichas.html"],
	["oterrestre_menu0.php","OTerrestreEMS.html"],
	["oterrestre_consultas.php","OTerrestreEMSConsultasPP.html"],
	["oterrestre_descargas.php","OTerrestreEMSDescargas.html"],
	["tr_resumen_individuo.php","OTerrestreEMSRelevaFicha.html"],
	["vw_seleccion_indi_index2.php","OTerrestreEMSRelevaIngresoSelecc.html"],
	["copula_borrar.php","OTerrestreEMSRelevaFichaCopulas.html"],
	["copula_crear.php","OTerrestreEMSRelevaFichaCopulas.html"],
	["copula_editar.php","OTerrestreEMSRelevaFichaCopulas.html"],
	["copula_index.php","OTerrestreEMSRelevaFichaCopulas.html"],
	["criades_borrar.php","OTerrestreEMSRelevaFichaDestete.html"],
	["criades_crear.php","OTerrestreEMSRelevaFichaDestete.html"],
	["criades_editar.php","OTerrestreEMSRelevaFichaDestete.html"],
	["temporada_editar.php","OTerrestreEMSRelevaFichaEdiCateg.html"],
	["individuo_editar.php","OTerrestreEMSRelevaFichaEdiDBasi.html"],
	["temporada_borrar.php","OTerrestreEMSRelevaFichaEliTempo.html"],
	["scan3D_borrar.php","OTerrestreEMSRelevaFichaEscaneo3.html"],
	["scan3D_crear.php","OTerrestreEMSRelevaFichaEscaneo3.html"],
	["scan3D_editar.php","OTerrestreEMSRelevaFichaEscaneo3.html"],
	["viaje_config.php","OTerrestreEMSRelevaFichaEtapaCOL.html"],
	["viaje_config_editar.php","OTerrestreEMSRelevaFichaEtapaCOL.html"],
	["instrumentos_colocados_borrar.php","OTerrestreEMSRelevaFichaEtapaINS.html"],
	["instrumentos_colocados_crear.php","OTerrestreEMSRelevaFichaEtapaINS.html"],
	["instrumentos_colocados_editar.php","OTerrestreEMSRelevaFichaEtapaINS.html"],
	["instrumentos_colocados_index.php","OTerrestreEMSRelevaFichaEtapaINS.html"],
	["viaje_borrar.php","OTerrestreEMSRelevaFichaEtapaVia.html"],
	["viaje_crear.php","OTerrestreEMSRelevaFichaEtapaVia.html"],
	["viaje_editar.php","OTerrestreEMSRelevaFichaEtapaVia.html"],
	["observado_crear.php","OTerrestreEMSRelevaFichaFechaLug.html"],
		["observado_borrar.php","OTerrestreEMSRelevaFichaFechaLug.html"],
	["observado_editar.php","OTerrestreEMSRelevaFichaFechaLug.html"],
	["fotos.php","OTerrestreEMSRelevaFichaFotos.html"],
	["hembra_borrar.php","OTerrestreEMSRelevaFichaHembra.html"],
	["hembra_crear.php","OTerrestreEMSRelevaFichaHembra.html"],
	["hembra_editar.php","OTerrestreEMSRelevaFichaHembra.html"],
	["macho_borrar.php","OTerrestreEMSRelevaFichaMacho.html"],
	["macho_crear.php","OTerrestreEMSRelevaFichaMacho.html"],
	["macho_editar.php","OTerrestreEMSRelevaFichaMacho.html"],
	["marca_borrar.php","OTerrestreEMSRelevaFichaMarcas.html"],
	["marca_crear.php","OTerrestreEMSRelevaFichaMarcas.html"],
	["marca_editar.php","OTerrestreEMSRelevaFichaMarcas.html"],
	["medidas_borrar.php","OTerrestreEMSRelevaFichaMedidas.html"],
	["medidas_crear.php","OTerrestreEMSRelevaFichaMedidas.html"],
	["medidas_editar.php","OTerrestreEMSRelevaFichaMedidas.html"],
	["muda_borrar.php","OTerrestreEMSRelevaFichaMuda.html"],
	["muda_crear.php","OTerrestreEMSRelevaFichaMuda.html"],
	["muda_editar.php","OTerrestreEMSRelevaFichaMuda.html"],
	["muestras_borrar.php","OTerrestreEMSRelevaFichaMuestras.html"],
	["muestras_crear.php","OTerrestreEMSRelevaFichaMuestras.html"],
	["muestras_editar.php","OTerrestreEMSRelevaFichaMuestras.html"],
	["temporada_crear.php","OTerrestreEMSRelevaFichaNueTempo.html"],
	["tag_borrar.php","OTerrestreEMSRelevaFichaTag.html"],
	["tag_crear.php","OTerrestreEMSRelevaFichaTag.html"],
	["tag_editar.php","OTerrestreEMSRelevaFichaTag.html"],
	["tag_index.php","OTerrestreEMSRelevaFichaTag.html"],
	["vinculo_madrehijo.php","OTerrestreEMSRelevaFichaVinMadre.html"],
	["vinculo_madrehijo_borrar.php","OTerrestreEMSRelevaFichaVinMadre.html"],
	["individuo_crear.php","OTerrestreEMSRelevaNueIndividuo.html"],
	["individuo_borrar.php","OTerrestreEMSRelevFichaEliIndivi.html"],
	["publicaciones_menu0.php","Publicaciones.html"],
	["vincular_IndiPubli_borrar.php","PublicacionesIndiEnPubli.html"],
	["vincular_IndiPubli_editar.php","PublicacionesIndiEnPubli.html"],
	["vincular_IndiPubli_todo.php","PublicacionesIndiEnPubli.html"],
	["publicaciones_borrar.php","PublicacionesNuestras.html"],
	["publicaciones_crear.php","PublicacionesNuestras.html"],
	["publicaciones_editar.php","PublicacionesNuestras.html"],
	["publicaciones_index.php","PublicacionesNuestras.html"],
	["publicaciones_ver.php","PublicacionesNuestras.html"],
	["vincular_IndiPubli_crear.php","PublicacionesVinculos.html"],
	["vincular_individuo_publicaciones.php","PublicacionesVinculos.html"],


	];


	n = uebJelp.length;
	esta=false;
	for (i = 0; i < n; i++) {		
	   if( lPagina == uebJelp[i][0] ) {
	       //mi=window.open("aayudote/"+uebJelp[i][1]);
		   mi=window.open("aayudote/"+uebJelp[i][1],"mi","",true);
		   esta = true;
		   break;
	      }  
	}
	if (esta==false){
		window.open("aayudote/Bienvenida.html");
		
	}
}
