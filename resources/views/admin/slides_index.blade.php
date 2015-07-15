@extends('admin.layout')

@section('title')
    Slides - Design - @parent
@stop

@section('page')
    <h2>Slides</h2>
    <hr>

    <div class="row">

        <div class="col-xs-4 tight-left module slides">
            <div class="module-header clearfix">
                <h6 class="title">Slides</h6>
            </div>

            <div class="module-content dropzone row slides-dropzone" id="slides-list" style="padding: 0; min-height: 80px;">
                <div class="dz-preview dz-file-preview preview-template clearfix">
                    <div class="col-xs-4 preview-image">
                        <div class="handle">
                            <img data-dz-thumbnail>
                        </div>
                    </div>
                    <div class="col-xs-6 preview-info">
                        <div class="dz-details">
                            <div class="dz-filename"><h6 data-dz-name></h6></div>
                            <div class="dz-size" data-dz-size></div>
                            <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
                            <div class="dz-success-mark"><i class="fa fa-check color-success"></i></div>
                            <div class="dz-error-mark"><i class="fa fa-close color-error"></i></div>
                            <div class="dz-error-message alert alert-error"><h6>Error</h6><p><span data-dz-errormessage></span></p></div>
                        </div>
                        <footer class="footer">
                            <div class="button-group">
                                <button class="small error delete"><i class="fa fa-trash"></i> Delete</button>
                            </div>
                        </footer>
                    </div>
                </div>

                @foreach($slides as $slide)
                    <div data-id="{{$slide->id}}" class="dz-sortable dz-preview dz-file-preview dz-success dz-complete preview-template">
                        <div class="col-xs-4 preview-image">
                            <div class="handle">
                                <img data-dz-thumbnail src="{{asset('images/' .$slide->name)}}">
                            </div>
                        </div>
                        <div class="col-xs-6 preview-info">
                            <div class="dz-details">
                                <div class="dz-filename"><h6 data-dz-name>{{$slide->name}}</h6></div>
                                <div class="dz-size" data-dz-size>{{filesize(public_path('images/' .$slide->name)) / 1000000}} MB</div>
                            </div>
                            <footer class="footer">
                                <div class="button-group image-actions">
                                    <button class="small error delete"><i class="fa fa-trash"></i> Delete</button>
                                </div>
                            </footer>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="col-xs-8 tight-right">
            <div class="module slide-editor">
                <header class="module-header clearfix" style="padding: 0">
                    <h6 class="pull-left">Slide #1</h6>
                    <div class="button-group pull-right">
                        <button class="add-object large primary"><i class="fa fa-plus"></i> Add Element</button>
                    </div>
                </header>

                <article class="module-content">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Element</th>
                                <th>Start Position</th>
                                <th>End Position</th>
                                <th>Delay</th>
                                <th>Animation Type</th>
                                <th>Animation Speed</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>

                            </tr>
                        </tbody>
                    </table>
                </article>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    @parent
    <script>
        var previewNode = document.querySelector(".preview-template");
        previewNode.id = "";
        var previewTemplate = previewNode.parentNode.innerHTML;
        previewNode.parentNode.removeChild(previewNode);

        $("#slides-list").dropzone({
            url: Friluft.URL + '/admin/slides',
            previewTemplate: previewTemplate,

            init: function() {
                //move the .dz-message to the start
                var el = $(this.element);
                el.find('.dz-message').detach().prependTo(el);

                this.on('sending', function(file, xhr, formData) {
                    formData.append('_token', Friluft.token);
                });
            }
        });

        //initialize sortable
        $("#slides-list").sortable({
            items: '.dz-sortable',
            handle: '.handle',
            forcePlaceholderSize: true,
            placeholder: '<div class="placeholder"></div>'
        });

        //persist changes of image ordering.
        $("#slides-list").on('sortupdate', function() {
            var tree = [];
            var order = 0;

            $("#slides-list .dz-sortable").each(function() {
                var id = $(this).attr('data-id');
                tree[id] = {id: id, order: order};
                order++;
            });

            console.log('tree:');
            console.log(JSON.stringify(tree));

            var payload = {
                _method: 'PUT',
                _token: Friluft.token,
                order: JSON.stringify(tree),
            };

            $.post(Friluft.URL + '/admin/slides/order', payload, function(response) {
                console.log('updated slide order, response:');
                console.log(response);
            });
        });

        //delete images
        $("#slides-list .image-actions .delete").click(function(e) {
            var preview = $(this).parents('.dz-sortable').first();
            e.preventDefault();
            var payload = {
                _method: 'DELETE',
                _token: Friluft.token,
                id: preview.attr('data-id')
            };

            $.post(Friluft.URL + '/admin/slides', payload, function(response) {
                preview.slideUp(function() {
                    $(this).remove();
                });
            });
        });
    </script>
@stop