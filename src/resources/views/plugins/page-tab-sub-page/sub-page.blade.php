@if ( isset($page->children) )
<div class="row justify-content-center mt-3">
    <div class="col-md-auto">
        <h2 class="ml-2">{{$page->title}}</h2>
    </div>
    <div class="col-md">
        <span class="preview">
            <a class='btn btn-outline-success ml-3' href="{{ $helper->url($page) }}" target="_blank" title="Preview">
                <i class='fas fa-external-link-square-alt mr-1'></i>{{$helper->t('preview')}}</a>
        </span>

        <span class="create_top_new_page">
            <a class='btn btn-outline-primary ml-3'
                href='{{route('LaravelCmsAdminPages.create',['parent_id' => $page->id, 'menu_enabled'=>0])}}'
                role='button'>
                <i class='fas fa-plus-circle mr-1'></i>{{$helper->t('create_new_sub_page')}}</a>
        </span>
    </div>

    <div class="col-md-12 mt-3">
        <ul id="sortableList" class="list-group all-pages">
            @php
            $all_items = ( $plugins['sub-page'] instanceof \Illuminate\Database\Eloquent\Collection) ?
            $plugins['sub-page'] : $page->children;
            @endphp

            @forelse ($all_items as $item)
            <li class="list-group-item list-group-item-action" id="page-{{$item->id}}"
                data-sort="{{$item->sort_value??0}}" data-id="{{$item->id}}">
                <i class="fas fa-arrows-alt text-secondary handle"></i>
                @php
                if ( $item->depth ){
                echo str_repeat("⎯⎯⎯", $item->depth);
                }
                if ( trim($item->redirect_url) != '' ) {
                $color_class = 'text-success';
                } else {
                $color_class = 'text-secondary';
                }
                if ( $item->slug == 'homepage'){
                $color_class = 'text-primary';
                }
                if ( $item->menu_enabled) {
                if ( $item->depth == 0 ){
                $icon = '<i class="fas fa-list-alt ml-1 ' . $color_class . ' "></i>';
                } elseif ( $item->depth == 1 ){
                $icon = '<i class="fas fa-list-ul ml-1 ' . $color_class . ' "></i>';
                } else {
                $icon = '<i class="fas fa-stream ml-1 ' . $color_class . ' "></i>';
                }

                } else {
                $icon = '<i class="far fa-file ml-1 ' . $color_class . ' "></i>';
                }
                if ( $item->slug == 'homepage'){
                $icon = '<i class="fas fa-home ml-1 ' . $color_class . ' "></i>';
                }
                @endphp

                {!! $icon !!}
                <a href="../{{$item->id}}/edit" title="{{$helper->t('sort_value')}} {{$item->sort_value ?? 0}}"
                    class="{{ $item->menu_enabled ? 'menu_enabled': ''}}">
                    @if ( $item->menu_title)
                    [ {{$item->menu_title}} ] -
                    @endif
                    {{$item->title}}
                </a>

                <a href="../{{$item->id}}/edit" class="text-secondary"><i class="far fa-edit ml-3"></i></a>

                <a href="{{$helper->url($item)}}" class="{{$color_class}}" target="_blank"><i
                        class="far fa-eye ml-3"></i></a>

                @if ( $item->menu_enabled)
                <a href="{{ route('LaravelCmsAdminPages.create', ['parent_id' => $item->id, 'menu_enabled'=>0]) }}"
                    class="text-secondary"><i class="far fa-plus-square ml-3"></i></a>
                @endif

                @if ( $item->slug == 'homepage')
                <span class="create_top_new_page">
                    <a class='btn btn-outline-info btn-sm ml-3'
                        href='{{route('LaravelCmsAdminPages.create',['switch_nav_tab'=>'settings'])}}' role='button'>
                        <i class='fas fa-plus-circle mr-1'></i>{{$helper->t('create_new_page')}}</a>
                </span>
                @else
                <a href="#del-{{$item->id}}" class="float-right delete-link" data-id="{{$item->id}}"
                    title="{{$helper->t('delete')}}" data-title="{{$item->title}}"><i class="far fa-trash-alt"></i></a>
                @endif
            </li>
            @empty
            <li class="list-group-item list-group-item-action p-5 mt-3">
                {{$helper->t('no_results')}}
            </li>
            @endforelse
        </ul>

    </div>
</div>
@endif

<input name="pages_new_order" id="pages_new_order" value="" class="form-control" type="hidden" />
<input name="pages_old_sort" id="pages_old_sort" value="" class="form-control" type="hidden" />
<input name="pages_new_sort" id="pages_new_sort" value="" class="form-control" type="hidden" />



<div class="m-3 text-info tips">
    Tips: Click and hold your mouse on the icon <i class="fas fa-arrows-alt text-secondary"></i>, drag up or down & drop
    to change the sort order of the sub pages.

    <a class='text-success ml-3 sort-top-level-menu' href="?switch_nav_tab=sub-page&sort_top_level_menu=yes">
        <i class="fas fa-sort mr-1"></i>{{$helper->t('sort_top_level_menu')}}</a>
</div>
<div class="w-100 p-5 m-5"></div>

@if ( !isset($page->children) )
<script>
    $('.nav-tabs .nav-link').each(function () {
        if ($(this).attr('href') == "#sub-page") {
            //$(this).addClass('d-none');
            $(this).addClass('disabled');
        }
    });
</script>
@endif

<script>
    function confirmDeleteSubPage(id, title){
        if ( !confirm("{{$helper->t('delete_message')}} " + title) ) {
            return false;
        }

        $.ajax({
            url : "{{route('LaravelCmsAdminPages.index',[],false)}}/" + id,
            type: 'DELETE',
            data : {
                _token: "{{ csrf_token() }}",
                response_type: "json"
            },
            // contentType: false,
            // cache: false,
            // processData:false,
            dataType: 'json',
            success: function (data) {
                console.log('Submission was successful.');
                //console.log(data);
                if ( data.success ){
                    $('#page-'+ id).fadeOut('slow');
                } else {
                    alert('Error: ' + data.error_message);
                }
            },
            error: function (data) {
                console.log('laravel-cms-page-delete : An error occurred.');
                console.log(data);
            },
        }).done(function(data){
            // console.log('laravel-cms-page-delete submitted');
            // console.log(data);
        });

        return false;
    }


    $(function(){
        $('.all-pages a.delete-link').click(function(e){
            e.preventDefault();
            var id = $(this).data('id');
            confirmDeleteSubPage(id, $(this).data('title'));
        });

        var sl = Sortable.create(document.getElementById("sortableList"), {
            animation: 500,
            onEnd: function(){
                var order = this.toArray();
                $('#pages_new_order').val( order );

                var oldSortValue  = [];
                var newSortValue  = [];
                var sortValueStep = 10;
                if ( order.length > 30000 ){
                    sortValueStep = 1;
                } else if ( order.length > 12000 ){
                    sortValueStep = 2;
                } else if ( order.length > 6000 ){
                    sortValueStep = 5;
                }
                order.forEach(function(pid, index){
                    oldSortValue.push($('#page-'+pid).data('sort'));
                    newSortValue.push(sortValueStep*(order.length - index));
                });
                $('#pages_old_sort').val( oldSortValue );
                $('#pages_new_sort').val( newSortValue );
            }
        });
        // var data = $("#sortableList").sortable('serialize');
        // console.log(data);

        $('i.handle').css('cursor','move').attr('title','Drag up or down & drop to change order');
    });



</script>
