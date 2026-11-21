<div class="content-wrapper">
    <input type="hidden" id="in_copiar_y_pegar">

    <section class="content-header">
        <h1>Ingreso de Calificaciones <small>Panel de Docentes</small></h1>
    </section>

    <section class="content">

        <!-- PANEL DE FILTROS SUPERIOR (ESTRUCTURA MODULAR BLINDADA ANTI-DESCUADRES) -->
        <div class="box box-solid box-primary" style="margin-bottom: 15px;">
            <div class="box-body" style="padding: 10px;">
                <div id="barra_principal" class="well well-sm clearfix" style="background-color: #f9f9f9; border-radius: 3px; margin-bottom: 0; padding: 15px; height: auto !important;">

                    <!-- 1. Fila Superior: Texto Informativo -->
                    <div class="row" style="margin-bottom: 10px;">
                        <div class="col-xs-12">
                            <label for="cboParalelos" class="control-label" style="font-size: 13px; font-weight: bold; margin-bottom: 0; color: #333; display: block; white-space: normal;">
                                <i class="fa fa-users text-blue"></i> Seleccione el Paralelo y la Asignatura para abrir la Sábana de Calificaciones:
                            </label>
                        </div>
                    </div>

                    <!-- 2. Fila Inferior: Controles alineados (Select a la izquierda, Botones a la derecha) -->
                    <div class="row" style="display: flex; align-items: center; flex-wrap: wrap;">

                        <!-- Selector Desplegable Unificado -->
                        <div class="col-md-7 col-sm-6 col-xs-12" style="margin-bottom: 5px;">
                            <select id="cboParalelos" class="form-control" style="width: 100% !important; height: 34px;">
                                <!-- Carga dinámica de paralelos y asignaturas agrupadas -->
                            </select>
                        </div>

                        <!-- Zona de Botones de Control -->
                        <div class="col-md-5 col-sm-6 col-xs-12 text-right" style="margin-bottom: 5px;">
                            <button type="submit" form="formulario_rubrica" id="ver_reporte" class="btn btn-sm" style="display: none; height: 34px; font-weight: bold; background-color: #ffffff !important; color: #1f7244 !important; border: 1px solid #1f7244 !important; padding: 6px 12px; border-radius: 3px; margin-right: 5px; transition: all 0.2s ease;">
                                <i class="fa fa-file-excel-o" style="color: #1f7244 !important; margin-right: 4px;"></i> Reporte Excel
                            </button>

                            <button type="button" id="save_all" class="btn btn-save-modern" style="display: none; height: 34px; font-weight: bold; padding: 6px 18px; border: none; border-radius: 4px; transition: all 0.3s ease;">
                                <i class="fa fa-save" style="margin-right: 6px;"></i> Guardar Todo
                            </button>
                        </div>

                    </div>

                    <!-- Inputs ocultos requeridos por tu sistema -->
                    <input id="id_estudiante" type="hidden" />
                    <input id="id_rubrica_personalizada" type="hidden" />
                    <input id="numero_pagina" type="hidden" />
                    <input id="id_paralelo" type="hidden" />
                    <input id="id_asignatura" type="hidden" />
                </div>
            </div>
        </div>

        <!-- ➡️ SECCIÓN PRINCIPAL: Sábana de Calificaciones (Ocupa el 100% del Ancho Libre) -->
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-solid box-success" id="pag_nomina_estudiantes" style="margin-bottom: 15px;">
                    <div class="box-header with-border" style="background-color: #00a65a; color: #fff;">
                        <h3 class="box-title" style="font-size: 14px; font-weight: bold; color: #fff; margin: 0; line-height: 30px;">
                            <i class="fa fa-users"></i> NÓMINA Y SÁBANA DE CALIFICACIONES GENERAL
                        </h3>
                    </div>

                    <div class="box-body" style="padding: 15px;">
                        <!-- Barra de Resumen e Información -->
                        <div id="total_registros_estudiantes" class="well well-sm" style="background-color: #f4f4f4; margin-bottom: 15px; padding: 10px 15px; border-radius: 3px;">
                            <div id="num_estudiantes" style="font-weight: bold; font-size: 13px;">
                                Estudiantes Matriculados en esta Materia: <span class="label label-primary" id="lbl_total_estudiantes" style="font-size: 12px; padding: 3px 8px;">0</span>
                            </div>
                        </div>

                        <form id="formulario_rubrica" action="calificaciones/reporte_sabana_calificaciones.php" method="post">
                            <input type="hidden" id="id_paralelo_excel" name="id_paralelo_excel" value="" />
                            <input type="hidden" id="id_asignatura_excel" name="id_asignatura_excel" value="" />

                            <div id="img_loader_estudiantes" class="text-center"></div>

                            <!-- 🔒 CONTENEDOR MAESTRO HERMÉTICO CON SCROLL HORIZONTAL DE SEGURIDAD -->
                            <div class="table-responsive-sabana">
                                <div id="lista_estudiantes_paralelo" style="width: 100%; min-width: 100%;">
                                    <div class="text-muted text-center" style="padding: 60px 20px; color: #999;">
                                        <i class="fa fa-hand-o-up fa-2x" style="margin-bottom: 10px; color: #ccc;"></i>
                                        <p style="margin: 0; font-size: 13px;">Seleccione un paralelo y una asignatura en el menú superior para cargar la nómina de alumnos...</p>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div> <!-- /.row -->

    </section>
</div>

<link rel="stylesheet" href="calificaciones/sabana_calificaciones_estilos.css">
<script src="calificaciones/index_unificado.js"></script>