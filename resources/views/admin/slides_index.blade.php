@extends('admin.layout')

@section('title')
    Slides - Design - @parent
@stop

@section('page')
    <h2>Slides</h2>
    <hr>

    <div class="row">
        <div class="module slide-editor" style="display: none;" data-id="">
            <header class="module-header clearfix" style="padding: 0">
                <h6 class="pull-left">Slide #1</h6>
                <div class="button-group pull-right">
                    <button class="add large primary"><i class="fa fa-plus"></i> Add Element</button>
                    <button class="save large success"><i class="fa fa-save"></i> Save</button>
                </div>
            </header>

            <?php
            $positions = [
                    'center' => 'Center',
                    'top' => 'Top',
                    'left' => 'Left',
                    'right' => 'Right',
                    'bottom' => 'Bottom',
                    'top_left' => 'Top Left',
                    'top right' => 'Top Right',
                    'bottom_left' => 'Bottom left',
                    'bottom_right' => 'Bottom Right',
            ];

            $types = [
                    'static' => 'Static',
                    'fade' => 'Fade',
                    'slide' => 'Slide',
                    'slide_grow' => 'Slide & Grow',
            ];
            ?>

            <article class="module-content">
                <table class="table elements">
                    <thead>
                    <tr>
                        <th>Content</th>
                        <th>Start Position</th>
                        <th>End Position</th>
                        <th>Offset</th>
                        <th>Delay (ms)</th>
                        <th>Animation Type</th>
                        <th colspan="2">Animation Speed (ms)</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="element-template" style="display: none;">
                        <td><input type="text" name="content" class="small" value="Lorem Ipsum" style="width: 100%"></td>
                        <td>{!! Former::select('start')->label(null)->options($positions)->class('small')->style('width: 100%') !!}</td>
                        <td>{!! Former::select('end')->label(null)->options($positions)->class('small')->style('width: 100%') !!}</td>
                        <td>
                            <div class="input-prepend" style="float: left; margin-right: 1rem; width: 80px;">
                                <span class="prepended">X</span>
                                <input type="number" name="offsetX" class="small" value="0" style="width: 100%;">
                            </div>

                            <div class="input-prepend" style="float: left; width:80px;">
                                <span class="prepended">Y</span>
                                <input type="number" name="offsetY" class="small" value="0" style="width: 100%;">
                            </div>
                        </td>
                        <td><input type="number" name="delay" value="0" class="small" style="width: 100%;"></td>
                        <td>{!! Former::select('type')->label(null)->options($types)->class('small')->style('width: 100%') !!}</td>
                        <td><input type="number" name="speed" value="250" class="small" style="width: 100%;"></td>

                        <td class="actions">
                            <div class="button-group">
                                <button class="tiny delete error"><i class="fa fa-trash"></i> Remove</button>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </article>
        </div>
        <hr>
    </div>

    <div class="row module slides">
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
                                <button class="small primary edit"><i class="fa fa-pencil"></i> Edit</button>
                                <button class="small error delete"><i class="fa fa-trash"></i> Delete</button>
                            </div>
                        </footer>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@stop

@section('scripts')
    @parent
    <script>
        var slideEditor = $(".slide-editor");
        var elementTemplate = $(".element-template").detach()
        elementTemplate.removeClass('element-template').addClass('element');


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

        //delete slide
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

        //edit slide
        $("#slides-list").on('click', '.image-actions .edit', function() {
            var el = $(this).parents('.dz-sortable').first();
            var id = el.attr('data-id');
            console.log('editing ' + id);

            //remove all elements
            slideEditor.attr('data-id', '');
            slideEditor.find('.elements tbody tr').remove();

            $.get(Friluft.URL + '/admin/slides/' + id, function(response) {
                console.log('response: ' );
                console.log(response);
                slideEditor.slideDown().attr('data-id', id);

                console.log(slideEditor);

                if (response.data != null) {
                    var element;
                    for(var i in response.data.elements) {
                        element = response.data.elements[i];
                        var elementEl = elementTemplate.clone();
                        slideEditor.find('.elements tbody').append(elementEl);
                        elementEl.slideDown();

                        elementEl.find('[name="content"]').val(element.content);
                        elementEl.find('[name="start"]').val(element.start);
                        elementEl.find('[name="end"]').val(element.end);
                        elementEl.find('[name="offsetX"]').val(element.offsetX);
                        elementEl.find('[name="offsetY"]').val(element.offsetY);
                        elementEl.find('[name="delay"]').val(element.delay);
                        elementEl.find('[name="type"]').val(element.type);
                        elementEl.find('[name="speed"]').val(element.speed);
                    }
                }
            });
        });

        //save slide
        slideEditor.find('.module-header button.save').click(function() {
            var id = slideEditor.attr('data-id');

            var elements = [];

            slideEditor.find('.module-content .elements tbody tr').each(function() {
                var element = {};

                element.content = $(this).find('[name="content"]').val();
                element.start = $(this).find('[name="start"]').val();
                element.end = $(this).find('[name="end"]').val();
                element.offsetX = $(this).find('[name="offsetX"]').val();
                element.offsetY = $(this).find('[name="offsetY"]').val();
                element.delay = $(this).find('[name="delay"]').val();
                element.type = $(this).find('[name="type"]').val();
                element.speed = $(this).find('[name="speed"]').val();

                elements.push(element);
            });

            var payload = {
                _method: 'PUT',
                _token: Friluft.token,
                data: JSON.stringify({elements: elements}),
            };

            $.post(Friluft.URL + '/admin/slides/' + id, payload, function(response) {
                console.log('Elements saved!');
            });
        });

        //add element
        slideEditor.find('.module-header button.add').click(function() {
            var el = elementTemplate.clone().show();
            slideEditor.find('.elements tbody').append(el);
        });

        //remove element
        slideEditor.on('click', '.element .actions .delete', function() {
            $(this).parents('.element').remove();
        });
    </script>
@stop