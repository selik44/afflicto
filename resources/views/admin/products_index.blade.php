@extends('admin.layout')

@section('title')
	@lang('admin.products') - @parent
@stop

@section('header')
    <h3 class="title">@lang('admin.products')</h3>
    {!! $filters !!}
@stop

@section('content')
    {!! $table !!}

    <div id="tableActions">
        <button disabled class="large move"><i class="fa fa-plane"></i> Move</button>
        <button disabled class="large delete error"><i class="fa fa-trash"></i> Delete</button>
    </div>

    <div class="modal fade center" id="deleteModal" style="width: 400px;">
        <a href="#" data-toggle-modal="#deleteModal" class="modal-dismiss"><i class="fa fa-close"></i></a>
        <div class="modal-header">
            <h3 class="end">Are you sure?</h3>
        </div>

        <div class="modal-content text-center">
            {!! Former::open()->method('DELETE')->action(route('admin.products.batch.destroy')) !!}

            {!! Former::hidden('products') !!}

            <div class="button-group flex">
                <button class="large error Delete">Yes, Delete</button>
                <a href="#" data-toggle-modal="#deleteModal" class="button large cancel">Cancel</a>
            </div>
            {!! Former::close() !!}
        </div>
    </div>

    <div class="modal fade center" id="moveModal">
        <a href="#" data-toggle-modal="#moveModal" class="modal-dismiss"><i class="fa fa-close"></i></a>
        <header class="modal-header">
            <h3 class="end">Move Products</h3>
        </header>

        <article class="modal-content" style="overflow: visible;">
            {!! Former::open()->method('PUT')->action(route('admin.products.batch.move')) !!}
            <?php
            use Friluft\Category;$cats = [];
            foreach(Category::orderBy('parent_id', 'asc')->orderBy('name', 'asc')->get() as $cat) {
                $cats[$cat->id] = $cat->name;
            }
            ?>
            {!! Former::select('categories')->name('categories[]')->multiple()->options($cats) !!}

            {!! Former::hidden('products') !!}
        </article>

        <footer class="modal-footer">
            <div class="button-group">
                <button class="large success update">Move</button>
                <button class="large cancel" data-toggle-modal="#moveModal">Cancel</button>
            </div>
        </footer>
        {!! Former::close() !!}
    </div>
@stop

@section('footer')
    {!! $pagination !!}
@stop

@section('scripts')
    @parent
    <script>
        var actions = $("#tableActions");
        var moveModal = $("#moveModal").gsModal();
        var deleteModal = $("#deleteModal").gsModal();

        var selectAll = $('table input[type="checkbox"][name="laratable-select-all"]');
        var selectRows = $('table input[type="checkbox"].laratable-select-row');
        var selectedProducts = [];

        //init chosen for categories select
        moveModal.find('select[name="categories[]"]').chosen({width: '100%'});

        //open move modal
        actions.find('.move').click(function() {
            selectedProducts = [];
            selectRows.each(function() {
                if ($(this).prop('checked')) {
                    selectedProducts.push($(this).attr('value'));
                }
            });

            moveModal.find('input[name="products"]').val(selectedProducts.join(','));

            moveModal.gsModal('show');
        });

        //batch delete
        actions.find('.delete').click(function() {
            selectedProducts = [];
            selectRows.each(function() {
                if ($(this).prop('checked')) {
                    selectedProducts.push($(this).attr('value'));
                }
            });

            deleteModal.find('input[name="products"]').val(selectedProducts.join(','));
            deleteModal.gsModal('show');
        });


        //quickly select all or none
        selectAll.change(function() {
            selectRows.prop('checked', $(this).prop('checked'));

            //is anything checked?
            if ($(this).prop('checked')) {
                //enable actions
                actions.find('input, button').removeAttr('disabled');
            }else {
                //disable actions
                actions.find('input, button').attr('disabled', 'disabled');
            }
        });

        //change select state of a single row
        selectRows.change(function() {
            selectAll.prop('checked', false);

            //is anything checked?
            var numChecked = 0;
            selectRows.each(function() {
                console.log('checking select row');
                if ($(this).prop('checked')) {
                    numChecked++;
                }
            });

            //is all checked?
            if (numChecked == selectRows.length) {
                selectAll.prop('checked', true);
            }

            //is anything checked?
            if (numChecked > 0) {
                //enable actions
                actions.find('input, button').removeAttr('disabled');
            }else {
                //disable actions
                actions.find('input, button').attr('disabled', 'disabled');
            }
        });

    </script>
@stop