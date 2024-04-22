@csrf
@if ($errors->any())
     <div class="text-red">
         <ul>
             @foreach ($errors->all() as $error)
                 <li>{{ $error }}</li>
             @endforeach
         </ul>
     </div>
 @endif
@if (session()->has('errorMessage'))
    <div class="text-red">
        {{ session()->get('errorMessage') }}
    </div>
@endif
