<?php
// templates/promotion_bar.php
$promo_path_prefix = $path_prefix ?? '';
?>
<div class="promotion-bar">
    <p>🎉 Special Offer! Get <strong>15% OFF</strong> on all sneakers this week! 
       <a href="<?php echo e($promo_path_prefix); ?>shop.php?filter=discounted" title="View discounted items">
           View Discounted Items
       </a> 
       (Promo automatically applied to sale items) 🎉
    </p>
</div>