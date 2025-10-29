@extends('admin.layouts.app')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="ic-datatable">
                {!! $dataTable->table(['id' => 'dataTableBuilder', 'class' => 'table table-bordered table-striped w-100 nowrap']) !!}
            </div>
        </div>
    </div>
@endsection

@push('style')
    @include('admin.layouts.partials.datatableCss')
@endpush

@push('script')
    @include('admin.layouts.partials.dataTablejs')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>

    <script>
        $(document).ready(function () {
            // Toggle sub-category
            $(document).on('click', '.toggle-subcategory-show', function () {
                const id = $(this).data('id');
                $('#sub-category-' + id).slideDown();
                $('#show-btn-' + id).addClass('d-none');
                $('#hide-btn-' + id).removeClass('d-none');
                setTimeout(initAllSortable, 300);
            });

            $(document).on('click', '.toggle-subcategory-hide', function () {
                const id = $(this).data('id');
                $('#sub-category-' + id).slideUp();
                $('#hide-btn-' + id).addClass('d-none');
                $('#show-btn-' + id).removeClass('d-none');
            });

            // Parent Sortable
            new Sortable($('#dataTableBuilder tbody')[0], {
                animation: 150,
                handle: 'td',
                onEnd: function () {
                    let ids = [];
                    $('#dataTableBuilder tbody tr').each(function () {
                        ids.push($(this).data('id'));
                    });

                    $.ajax({
                        url: '{{ route("categories.reorder") }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            ids: ids
                        },
                        success: function (res) {
                            toastr.success(res.message);
                            console.log(res.message);
                        },
                        error: function () {
                            toastr.error('Parent sort failed.');
                        }
                    });
                }
            });
        });

        function initAllSortable() {
            $('.sub-category-list').each(function () {
                const el = this;
                new Sortable(el, {
                    animation: 150,
                    onEnd: function () {
                        const sortedIDs = $(el).find('.sortable-subcategory-item').map(function () {
                            return $(this).data('id');
                        }).get();
                        const categoryId = $(el).data('category-id');

                        $.ajax({
                            url: '{{ route("subcategories.reorder") }}',
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                category_id: categoryId,
                                ids: sortedIDs
                            },
                            success: function (res) {
                                toastr.success(res.message);
                                console.log(res.message);
                            },
                            error: function () {
                                toastr.error('Sub-category sort failed.');
                            }
                        });
                    }
                });
            });
        }
    </script>
@endpush
