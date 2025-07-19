<?php


// function bladeGetPaymentLogos($logoType)
// {
//     $logoName = [
//         'bank' => 'bankLogos',
//         'eWallet' => 'eWalletLogos',
//         'otherEWallet' => 'otherEWalletLogos',
//         'card' => 'cardLogos',
//     ];

//     $logo = $logoName[$logoType];

//     return config("constants.{$logo}");
// }

function setActiveCategory($category, $output = 'active')
{
    return request()->category == $category ? $output : '';
}

function bldRtMap($registeredParticipants, $totalParticipants)
{
    $stylesEventRatio = '';

    if ($totalParticipants === 0 || $totalParticipants === null) {
        $ratio = 0;
    } else {
        $ratio = (float) $registeredParticipants / $totalParticipants;
    }

    if ($ratio > 0.9) {
        $stylesEventRatio .= 'background-color: #EF4444; color: white;';
    } elseif ($ratio === 0) {
        $stylesEventRatio .= 'background-color: #f9b82a; color: white;';
    } elseif ($ratio > 0.5) {
        $stylesEventRatio .= 'background-color: #FA831F; color: white;';
    } elseif ($ratio <= 0.5) {
        $stylesEventRatio .= 'background-color: #FFE325; color: #2e4b59;';
    }

    return $stylesEventRatio;
}




function bldImg($eventBanner)
{
    $imgFailure = asset('assets/images/404.png');
    if ($eventBanner) {
        $eventBannerImg = asset('storage/'.$eventBanner);
    } else {
        $eventBannerImg = $imgFailure;
    }

    return $eventBannerImg;
}



function bldImgF()
{
    $imgFailure = asset('assets/images/404.png');

    return "onerror=\"this.onerror=null;this.src='{$imgFailure}';\"";
}


function getNumbers()
{
    $discount = session()->get('coupon')['discount'] ?? 0;
    $code = session()->get('coupon')['name'] ?? null;
    
    $userId = auth()->id();
    $cart = $userId ? \App\NewCart::getUserCart($userId) : null;
    
    $cartSubtotal = $cart ? $cart->getSubTotal() : 0;
    $newSubtotal = ($cartSubtotal - $discount);
    if ($newSubtotal < 0) {
        $newSubtotal = 0;
    }
    $newTotal = $newSubtotal;

    return collect([
        'discount' => $discount,
        'code' => $code,
        'newSubtotal' => $newSubtotal,
        'newTotal' => $newTotal,
    ]);
}

function getStockLevel($quantity)
{
    $stockThreshold = config('app.stock_threshold', 5);
    
    if ($quantity > $stockThreshold) {
        $stockLevel = '<div class="btn btn-success">In Stock</div>';
    } elseif ($quantity <= $stockThreshold && $quantity > 0) {
        $stockLevel = '<div class="btn btn-warning">Low Stock</div>';
    } else {
        $stockLevel = '<div class="btn btn-danger">Not available</div>';
    }

    return $stockLevel;
}

function getValidCartQuantity()
{
    $validQuantity = 0;
    $userId = auth()->id();
    $cart = $userId ? \App\NewCart::getUserCart($userId) : null;
    
    if ($cart) {
        foreach ($cart->getContent() as $item) {
            if ($item->product) {
                $validQuantity += $item->quantity;
            }
        }
    }
    return $validQuantity;
}




function bladeEventGameImage($eventBanner)
{
    if ($eventBanner) {
        $eventBannerImg = asset('storage/'.$eventBanner);
    } else {
        $eventBannerImg = asset('assets/images/createEvent/question.png');
    }

    return $eventBannerImg;
}

function bldLowerTIer($eventTier)
{
    return $eventTier ? strtolower($eventTier) : 'no-tier';
}

function bldOrdinal($number)
{
    $number = intval($number);

    if ($number % 100 >= 11 && $number % 100 <= 13) {
        return $number.'th';
    }

    switch ($number % 10) {
        case 1:
            return $number.'st';
        case 2:
            return $number.'nd';
        case 3:
            return $number.'rd';
        default:
            return $number.'th';
    }
}

function bldPlural($amount, $singular = '', $plural = 's')
{
    if ($amount === 1) {
        return $singular;
    }

    return $plural;
}
