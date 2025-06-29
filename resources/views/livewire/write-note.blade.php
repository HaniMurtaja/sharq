<div>
    <div class="modal-backdrop fade"></div>
    <div class="modal fade show " style="display: block;">
        <div class="modal-dialog modal-dialog-centered my-modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                       Write note
                    </h5>

                    <button type="button" class="close" wire:click="$dispatch('closeModal')">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <textarea class="form-control" style="width: 100%;"
                                        wire:model.live="message">
                                    </textarea>
                                    <div style="color:red">
                                        @error('message')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>
                    </form>

                    <br>

                    <div class="modal-footer">
                        <button class="btn" style="background: linear-gradient(180deg, #A30133 0%, #002A5E 100%); color:#f7f7f7; height:38px"
                            wire:click.prevent="writeNote" type="button">
                            Save
                        </button>
                        <button class="btn btn-danger" type="button" wire:click.prevent="$dispatch('closeModal')">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
