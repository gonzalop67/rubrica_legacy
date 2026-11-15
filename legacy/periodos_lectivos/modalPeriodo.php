<!-- MODAL: Nuevo / Editar Periodo de Evaluación (AdminLTE 2) -->
<div class="modal fade" id="modalAcademico" tabindex="-1" role="dialog" aria-labelledby="modalAcademicoTitle">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="modalAcademicoTitle"><strong>Registrar Bloque Académico</strong></h4>
            </div>
            
            <form id="form-academico" onsubmit="guardarAcademico(event)">
                <!-- Llave foránea del Año Lectivo -->
                <input type="hidden" name="id_periodo_lectivo" id="hidden-lectivo-id">
                <!-- ID del Periodo de Evaluación (vacío al insertar, con ID al editar) -->
                <input type="hidden" name="id_periodo_evaluacion" id="edit-academico-id">
                
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-xs-8">
                            <label for="pe_nombre">Nombre del Bloque</label>
                            <input type="text" name="pe_nombre" id="pe_nombre" class="form-control" maxlength="24" placeholder="Ej: Primer Trimestre" required>
                        </div>
                        <div class="form-group col-xs-4">
                            <label for="pe_abreviatura">Abreviatura</label>
                            <input type="text" name="pe_abreviatura" id="pe_abreviatura" class="form-control text-uppercase" maxlength="6" placeholder="Ej: 1T" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="form-group col-xs-6">
                            <label for="id_tipo_periodo">Tipo de Periodo</label>
                            <select name="id_tipo_periodo" id="id_tipo_periodo" class="form-control" required>
                                <option value="">-- Seleccione --</option>
                                <!-- Se llena dinámicamente desde sw_tipo_periodo -->
                            </select>
                        </div>
                        <div class="form-group col-xs-3">
                            <label for="pe_ponderacion">Ponderación (%)</label>
                            <input type="number" name="pe_ponderacion" id="pe_ponderacion" class="form-control" step="0.01" min="0" max="100" placeholder="Ej: 33.33" required>
                        </div>
                        <div class="form-group col-xs-3">
                            <label for="pe_orden">Orden</label>
                            <input type="number" name="pe_orden" id="pe_orden" class="form-control" min="1" placeholder="Ej: 1" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="form-group col-xs-6">
                            <label for="pe_fecha_inicio">Fecha Inicio</label>
                            <input type="date" name="pe_fecha_inicio" id="pe_fecha_inicio" class="form-control" required>
                        </div>
                        <div class="form-group col-xs-6">
                            <label for="pe_fecha_fin">Fecha Fin</label>
                            <input type="date" name="pe_fecha_fin" id="pe_fecha_fin" class="form-control" required>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm pull-left" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-sm" id="btnGuardarAcademico">Guardar Bloque</button>
                </div>
            </form>
        </div>
    </div>
</div>
