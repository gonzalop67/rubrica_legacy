<!-- MODAL: Nuevo / Editar Parcial de Evaluación (AdminLTE 2) -->
<div class="modal fade" id="modalParcial" tabindex="-1" role="dialog" aria-labelledby="modalParcialTitle">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="modalParcialTitle"><strong>Registrar Parcial de Evaluación</strong></h4>
            </div>
            
            <form id="form-parcial" onsubmit="guardarParcial(event)">
                <!-- Input oculto para vincularlo al bloque académico seleccionado -->
                <input type="hidden" name="periodo_academico_id" id="hidden-academico-id">
                <!-- Almacena el ID del Parcial a EDITAR -->
                <input type="hidden" name="id" id="edit-parcial-id">
                
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-xs-8">
                            <label for="parcial_nombre">Nombre del Parcial</label>
                            <input type="text" name="nombre" id="parcial_nombre" class="form-control" placeholder="Ej: Parcial 1" required>
                        </div>
                        <div class="form-group col-xs-4">
                            <label for="parcial_peso_nota">Peso Nota (%)</label>
                            <input type="number" name="peso_nota" id="parcial_peso_nota" class="form-control" step="0.01" min="0.01" max="100" placeholder="30" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="parcial_orden">Orden Secuencial</label>
                        <input type="number" name="orden" id="parcial_orden" class="form-control" min="1" placeholder="Ej: 1" required>
                    </div>
                    
                    <div class="row">
                        <div class="form-group col-xs-4">
                            <label for="parcial_fecha_inicio">Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" id="parcial_fecha_inicio" class="form-control" required>
                        </div>
                        <div class="form-group col-xs-4">
                            <label for="parcial_fecha_fin">Fecha Fin</label>
                            <input type="date" name="fecha_fin" id="parcial_fecha_fin" class="form-control" required>
                        </div>
                        <div class="form-group col-xs-4">
                            <label for="parcial_fecha_cierre">Cierre Notas</label>
                            <input type="date" name="fecha_cierre_notas" id="parcial_fecha_cierre" class="form-control" required>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm pull-left" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-sm" id="btnGuardarParcial">Guardar Parcial</button>
                </div>
            </form>
        </div>
    </div>
</div>
