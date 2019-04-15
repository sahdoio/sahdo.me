<div id="mediaTypeModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered" style="margin-top: -10%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Escolha um tipo:</h4>
            </div>
            <div class="modal-body">
                <div class="select-wrapper center">
                    <select id="type-select" class="selectpicker center" name="type_id" data-size="7" data-style="btn btn-primary btn-round" title="Selecionar">
                        @foreach($types as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button id="btn-select-modal" type="button" class="btn btn-primary">Selecionar</button>
            </div>
        </div>
    </div>
</div>