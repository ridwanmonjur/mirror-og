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
@if (isset($errorMessage))
    <div class="text-danger">
        {{ $errorMessage }}
    </div>
@endif
