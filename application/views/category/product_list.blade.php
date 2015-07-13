<div class="row-fluid theBlock">
    <div class="span12">
        <?php $counter = 0;?>
		<?php $skip = false; ?>
        <div class="row-fluid">
            @foreach($result as $item)
            @if($counter % 4 == 0)
        </div>
        <div class="row-fluid">
            @endif
			@if($item->getImages)
            <?php $images = getProductImages($item) ?>
            @endif
            <?php $price = getItemPrice($item) ?>
            <div class="span3">
                <div class="thumbnail" style="padding: 0">
                    <div style="padding:4px">
                        @if($item->getImages)
                        <a href="{{URL::base()}}/product/<?php echo $item->alias ?>" class="thumbnailimg">
                            <img alt="{{$item->getDetail->name}}" src="{{$images->main->small}}" style="display:block;margin:0 auto;" /></a>
                        @else
                        <img style="display:block;margin:0 auto;" alt="300x200" style="width: 100%" src="https://placehold.it/200x150">
                        @endif
                    </div>
                    <a href="{{URL::base()}}/product/<?php echo $item->alias ?>">
                        <div class="caption">
                            <b>{{$item->getDetail->name}}</b></small>
                            <p class="variant"><small>{{$item->getDetail->variant}}</small></p>
                            @if($item->discount || $item->getDiscount)
                            <p class="old-price">{{$price->get_taxed}}</p>
                            <p class="new-price">{{$price->get_discount}}</p>
                            @else
                            <p class="new-price">{{$price->get_taxed}}</p>
                            @endif
                        </div>
                    </a>
                    @if($item->discount || $item->getDiscount)
                    <div class="modal-footer" style="text-align: left">
                        <div class="row-fluid">
                            @if($item->getDiscount && empty($item->discount))
                            <div class="span12"><b class="text-info text-center" style="display:block;"><small>{{$item->getDiscount->description}}</small></b></div>
                            @endif
                            <div class="row-fluid">
                                <div class="span6"><img src="/img/discount-badge.png"><span class="discount-title"><b>{{($item->discount) ? $item->discount : $item->getDiscount->discount_value}}</b></span></div>
                                <div class="span6" style="border-left:1px solid #ddd;padding-left:5px;"><b>Kazancınız</b><br/><small>{{$price->totaldiscount}}</small></div>
                            </div>
                        </div>
                    </div>
                    @endif
                    <a title='{{$item->getDetail->name}} {{$item->getDetail->variant}}' href="{{URL::base()}}/product/<?php echo $item->alias ?>" class="free-shipment"></a>
                </div>
            </div>
            <?php $counter++; ?>
            @endforeach
        </div>
    </div>