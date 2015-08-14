@extends('admin.layout')

@section('title')
    @lang('admin.orders') - @parent
@stop

@section('header')
    <h3 class="title">@lang('admin.orders')</h3>
    {!! $filters !!}
@stop

@section('content')
    {!! $table !!}

    <div id="status-modal" class="modal">
        <div class="modal-header">Status</div>
        <div class="modal-content">
            <select name="status">
                <option value="unprocessed">@lang('admin.status.unprocessed')</option>
                <option value="written_out">@lang('admin.status.written_out')</option>
                <option value="delivered">@lang('admin.status.delivered')</option>
                <option value="cancelled">@lang('admin.status.cancelled')</option>
                <option value="ready_for_sending">@lang('admin.status.ready_for_sending')</option>
                <option value="processed">@lang('admin.status.processed')</option>
                <option value="unused">@lang('admin.status.unused')</option>
            </select>
        </div>
        <div class="modal-footer">
            <div class="button-group">
                <button class="success update">Update</button>
                <button class="cancel" data-toggle-modal="#status-modal">Cancel</button>
            </div>
        </div>
    </div>
@stop

@section('footer')
    <div id="tableActions" class="button-group">
        <button disabled class="packlist large primary"><i class="fa fa-download"></i> Packlist</button>
        <button disabled class="update-status large success" data-toggle-modal="#status-modal"><i class="fa fa-download"></i> Update Status</button>
    </div>
    {!! $pagination !!}
@stop

@section('scripts')
	@parent
	<script>

		var actions = $("#tableActions");
		var selectAll = $('table input[type="checkbox"][name="laratable-select-all"]');
		var selectRows = $('table input[type="checkbox"].laratable-select-row');

        function getSelectedOrders() {
            var orders = [];
            selectRows.each(function() {
                if ($(this).prop('checked')) {
                    orders.push($(this).attr('value'));
                }
            });

            return orders.join(',');
        }

		//request packlist
		var packListAction = "{{route('admin.orders.multipacklist')}}";
		actions.find('.packlist').click(function() {
			var url = "{{route('admin.orders.multipacklist', '')}}/" + getSelectedOrders();
			window.location.href = url;
		});

        //update status
        $("#status-modal").find('.modal-footer button.update').click(function() {
            var status = $("#status-modal .modal-content select").val();
            var url = Friluft.URL + '/admin/orders/status/' + getSelectedOrders() + '/' + status;
            window.location.href = url;
        });

		//quickly select all or none
		selectAll.change(function() {
			selectRows.prop('checked', $(this).prop('checked'));

            //is anything checked?
            var numChecked = 0;
            selectRows.each(function() {
                console.log('checking select row');
                if ($(this).prop('checked')) {
                    numChecked++;
                }
            });

            //is anything checked?
            if (numChecked > 0) {
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