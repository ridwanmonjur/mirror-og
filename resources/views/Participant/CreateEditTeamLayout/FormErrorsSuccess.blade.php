@csrf
@if ($errors->any())
     <div class="text-danger">
         <ul>
             @foreach ($errors->all() as $error)
                 <li>{{ $error }}</li>
             @endforeach
         </ul>
     </div>
 @endif
@if (session()->has('errorMessage'))
    <div class="text-danger">
        {{ session()->get('errorMessage') }}
    </div>
@endif
