
    
    <li class="nav-item"><a href="{{ route('cart.index') }}" class="nav-link">Cart
    @if (getValidCartQuantity() > 0)
    <span class="badge badge-pill badge-warning"><span>{{ getValidCartQuantity() }}</span></span>
    @endif
    </a></li>
    {{-- @foreach($items as $menu_item)
        <li>
            <a href="{{ $menu_item->link() }}">
                {{ $menu_item->title }}
                @if ($menu_item->title === 'Cart')
                    @if (Cart::getTotalQuantity() > 0)
                    <span class="cart-count"><span>{{ Cart::getTotalQuantity() }}</span></span>
                    @endif
                @endif
            </a>
        </li>
    @endforeach --}}
