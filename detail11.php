<?php
include './include/header.php';
//print_r($_SESSION);
$login_id = '';
if ($_SESSION) {
    if (isset($_SESSION['user_id']))
        $login_id = $_SESSION['user_id'];
    if (isset($_SESSION['DH_seller_id'])) {
        $login_id = $_SESSION['DH_seller_name'];
        $loginSeller = $_SESSION['DH_seller_type_name'];
    }
}

$UriSegmentID = $ajGenObj->getUriSegment(3);
if (($UriSegmentID)) {
    $id = $njEncryptionObj->safe_b64decode($UriSegmentID);
    $condition = "`product_id` = " . $id . " ";
    $result = $dbComObj->viewData($conn, "products", "*", $condition);
    $data = $dbComObj->fetch_object($result);
    // image
    $condition_image = "`product_id` = " . $id . " ";
    $result_image = $dbComObj->viewData($conn, "product_image", "*", $condition_image);

    if ($data == "") {
        header("Location: " . BASE_URL . "404/"); /* Redirect browser */
        exit();
    }
}
if ($data->seller_id) {
    $sellername = $ajGenObj->NameById($data->seller_id, 'users');
    $url_sid = $njEncryptionObj->safe_b64encode($data->seller_id);
    $seller_slug = $njGenObj->removeSpecialChar($sellername);
    $seller_page = BASE_URL . 'seller-shop/' . $url_sid . '/' . $seller_slug . '/';
} else {
    $sellername = '';
    $seller_page = '';
}
   $group_products =$data->group_products;
            if($group_products){
                $group_products_arr = explode(',', $group_products);
            }else {
                $group_products_arr =array(); 
            }

$currency_symbol = CURRENCY_SYMBOL;

$category_name = $ajGenObj->NameById($data->category_id, 'category');
$cat_encode_id = $njEncryptionObj->safe_b64encode($data->category_id);
$category_segment = $ajGenObj->getUriSegment(5);
$category_page = BASE_URL . 'grid/' . $cat_encode_id . '/type/' . $category_segment;
?>
<style>
    .loading-overlay{
        position: fixed;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        z-index: 999;
        background: rgba(183, 173, 173, 0.5) url(<?php echo BASE_URL; ?>img/loading.gif) no-repeat center center;
        /*background: rgba(0,0,0,0.5);*/
        display: none;
    }
    .sr-msg-error {
        border: 1px solid red;
        color: red !important;
    }
</style>
<div id="loading-overlay" class="loading-overlay"></div>     
<div class="detail">
    <div class="container">
         <div  id="resajmsg"> </div>
        
        <ol class="breadcrumb">

            <li><a href="<?php echo BASE_URL; ?>">Home</a></li>
            <li><a href="<?php echo $category_page; ?>"><?php echo $category_name; ?></a></li>
            <?php
            if (isset($category_id) && $category_id) {
                $condition_cat = "1 and `id` = " . $category_id . "";
                $result_cat = $dbComObj->viewData($conn, "category", "*", $condition_cat);
                $data_cat = $dbComObj->fetch_assoc($result_cat)
                ?>
                <li class="active"><?php echo $data_cat['name']; ?></li>
                <?php
            } elseif (isset($Brand_id) && $Brand_id) {
                $condition_cat = "1 and `id` = " . $Brand_id . "";
                $result_cat = $dbComObj->viewData($conn, "brands", "*", $condition_cat);
                $data_cat = $dbComObj->fetch_assoc($result_cat)
                ?>
                <li class="active"><?php echo $data_cat['name']; ?></li>
            <?php } else {
                ?>
                <li class="active"> <?php echo $data->product_name; ?></li>
            <?php } ?>
        </ol>
        <div class="row">

            <div class="col-md-6 col-sm-6">
                <div id="main_area">
                    <!-- Slider -->
                    <div class="row">

                        <div class="full-width">
                            <div class="col-xs-12" id="slider">
                                <!-- Top part of the slider -->
                                <div class="row">
                                    <div class="col-sm-12" id="carousel-bounding-box">
                                        <div class="carousel slide" id="myCarousel">
                                            <!-- Carousel items -->
                                            <div class="carousel-inner">


                                                <!--  <div class="active item" data-slide-number="3">
                                                     <img src="<?php // echo BASE_URL;     ?>frontend/img/ring-1.png"></div>
             
                                                 <div class="item" data-slide-number="1">
                                                     <img src="<?php //echo BASE_URL;     ?>frontend/img/ring-2.png"></div> -->
                                                <?php
                                                $numimage = $dbComObj->num_rows($result_image);
                                                if ($numimage > 0) {
                                                    $slide_id = 1;

                                                    while ($product_image = $dbComObj->fetch_object($result_image)) {

                                                        $thumb_image = BASE_URL . 'images/products/thumb/' . $product_image->thumb_image;
                                                        $image = BASE_URL . 'images/products/' . $product_image->image;
                                                        ?>

                                                        <div class="item <?php if ($slide_id == 1) echo "active"; ?>" data-slide-number="<?php echo $slide_id; ?>">
                                                            <img src="<?php echo $image; ?>"></div>
                                                        <?php
                                                        $slide_id++;
                                                    }
                                                } else {
                                                    ?>
                                                    <div class="active item" data-slide-number="3">
                                                        <img src="<?php echo BASE_URL; ?>frontend/img/default-product-image.png"></div>

                                                <?php }
                                                ?>


                                                <!-- Carousel nav -->
                                                <!--<a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
                                                    <span class="glyphicon glyphicon-chevron-left"></span>
                                                </a>
                                                <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
                                                    <span class="glyphicon glyphicon-chevron-right"></span>
                                                </a>-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--/Slider-->

                            <div class="full-width" id="slider-thumbs">
                                <!-- Bottom switcher of slider -->
                                <ul class="hide-bullets">
                                    <?php
                                    $numimage = $dbComObj->num_rows($result_image);
                                    if ($numimage > 0) {
                                        $thumbs_id = 1;
                                        $result_thumb = $dbComObj->viewData($conn, "product_image", "*", $condition_image);
                                        while ($thumb_image = $dbComObj->fetch_object($result_thumb)) {

                                            $thumb_image_p = BASE_URL . 'images/products/thumb/' . $thumb_image->thumb_image;
                                            ?>
                                            <li class="col-sm-3 col-xs-4">
                                                <a class="thumbnail" id="carousel-selector-<?php echo $thumbs_id; ?>"><img src="<?php echo $thumb_image_p; ?>" class="img-responsive">
                                                </a>
                                            </li>
                                            <?php
                                            $thumbs_id++;
                                        }
                                    } else {
                                        ?>
                                        <li class="col-sm-3 col-xs-4">
                                            <a class="thumbnail" id="carousel-selector-3"><img src="<?php echo BASE_URL; ?>frontend/img/default-product-image.png" class="img-responsive">
                                            </a>
                                        </li>

                                    <?php }
                                    ?>


                                    <!--    <li class="col-sm-3 col-xs-4">
                                           <a class="thumbnail" id="carousel-selector-3"><img src="<?php echo BASE_URL; ?>frontend/img/ring-1.png" class="img-responsive">
                                           </a>
                                       </li>
                   
                                       <li class="col-sm-3 col-xs-4">
                                           <a class="thumbnail" id="carousel-selector-1"><img src="<?php echo BASE_URL; ?>frontend/img/ring-2.png" class="img-responsive"></a>
                                       </li>
                   
                                       <li class="col-sm-3 col-xs-4">
                                           <a class="thumbnail" id="carousel-selector-2"><img src="<?php echo BASE_URL; ?>frontend/img/ring-1.png" class="img-responsive"></a>
                                       </li> -->

                                </ul>
                            </div>
                             
                            <a data-slide="prev" href="#myCarousel" class="left carousel-control"><i class="fa fa-angle-left"></i></a>
                            <a data-slide="next" href="#myCarousel" class="right carousel-control"><i class="fa fa-angle-right"></i></a>

                        </div>

                    </div>
                    <script type="text/javascript">
                        jQuery(document).ready(function ($) {

                            $('#myCarousel').carousel({
                                interval: 5000
                            });

                            //Handles the carousel thumbnails
                            $('[id^=carousel-selector-]').click(function () {
                                var id_selector = $(this).attr("id");
                                try {
                                    var id = /-(\d+)$/.exec(id_selector)[1];
                                    console.log(id_selector, id);
                                    jQuery('#myCarousel').carousel(parseInt(id));
                                } catch (e) {
                                    console.log('Regex failed!', e);
                                }
                            });
                            // When the carousel slides, auto update the text
                            $('#myCarousel').on('slid.bs.carousel', function (e) {
                                var id = $('.item.active').data('slide-number');
                                $('#carousel-text').html($('#slide-content-' + id).html());
                            });
                        });
                    </script>


                </div>


            </div>


            <div class="col-md-6 col-sm-6">
                <div class="green-box-outline">
                    <p class="seller-name"><!--Seller/Designer Name :--><b><a href="<?php echo $seller_page; ?>"><?php echo $sellername; ?></a></b></p>
                    <h4 class="hadding text-left"><?php echo $data->product_name; ?></h4>
                    <p class="border-bottom-2"><a href="#"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-half-o"></i>  <span>193 Customer Reviews</span></a> </p>
                     <?php 
                              $product_price = '0.00';
                             // echo ($data->product_type);
                             if($data->product_type == "grouped"){
                                  if ($group_products_arr) {
                                    $product_price_grp=0;
                                     for ($x = 0; $x < count($group_products_arr); $x++) {
                                   $condition_grp = "`product_id` = " . $group_products_arr[$x] . "  ";
                                    $result_grp = $dbComObj->viewData($conn, "products", "*", $condition_grp);
                                    $data_grp = $dbComObj->fetch_object($result_grp); 
                                    if ($data_grp->price &&  $data->is_in_stock == '1')
                                        $product_price_grp += $data_grp->price;
                                  }  
                                }
                                $product_price = number_format($product_price_grp, 2);
                            }else {
                                 if ($data->price)  $product_price = number_format($data->price, 2);
                         
                           }
                           ?>
                    <p class="font-18"> <?php echo $currency_symbol; ?>
                        <span id="product_price"><?php echo $product_price;  ?></span>    
                        <span class="pull-right">
                            <?php
                            if ($data->is_in_stock == '1')
                                echo ' In Stock';
                            else
                                echo 'Out of Stock';
                            ?>

                        </span></p>

                    <div id="associated_check_list">
                        <?php   //$data->associated_check_list;
                          $conditionassocated = "1 and associated_product_id='" . $id . "' order by id";
                        $resultACL1 = $dbComObj->viewData($conn, "configure_attribute_option", "*", $conditionassocated);
                        $numACL1 = $dbComObj->num_rows($resultACL1);
                        $dataACL1 = $dbComObj->fetch_assoc($resultACL1);
                          $product_id = $dataACL1['product_id'];
                          if($product_id &&  $numACL1) {
                             $product_id= $dataACL1['product_id'];
                          } else {
                              $product_id= $id;
                          }
                           $product_id;
                        $conditionACL = "1 and product_id='" . $product_id . "' order by id";
                        $resultACL = $dbComObj->viewData($conn, "configure_attribute_option", "*", $conditionACL);
                          $numACL = $dbComObj->num_rows($resultACL);
                          

                        if ($numACL > 0) {
                            echo '<div class="row form-inline" >';
                            $i = 1;
                            $ii;
                            while ($dataACL = $dbComObj->fetch_assoc($resultACL)) {
                                //    print_r($dataACL['attribute_id']);
                                $assocated_pid=$dataACL['associated_product_id'];
                                $url_idacl = $njEncryptionObj->safe_b64encode($assocated_pid);
                               $assocated_slug = $ajGenObj->NameById($assocated_pid, 'products','slug','product_id');
                                $assocated_page = BASE_URL . 'detail/' . $url_idacl . '/' . $assocated_slug . '/';
                                if ($ii == $dataACL['attribute_id']) {
                                    $ii = $dataACL['attribute_id'];
                                     //echo '<br>';
                                    //  echo 'aaaa=='.$ii;
                                } else {
                                    $ii = $dataACL['attribute_id'];
                                    echo '<label > ' . $dataACL['attribute_name'] . ' </label>';
                                    // echo '=mmmm=='.$ii;
                                }
                                ?>

                                <div class="form-group">

                                    <a href="<?php echo $assocated_page; ?>"  class="btn btn-default "><?php echo $dataACL['option_name'] ?> </a>
                                </div>

                                <?php
                            }
                            echo '</div>';
                        }
                      
                        ?>

                    </div>
                    <div class="full-width">
                        <div class="Col-md-12 col-sm-12 col-xs-12">
                            <div class="row">
                                <?php
                                // echo $thumb_image
                                /* For Cart Function */
                                // image code start
                                $condition_image_cart = "`product_id` = " . $id . " ORDER BY RAND()  LIMIT 1";
                                $result_imagec = $dbComObj->viewData($conn, "product_image", "*", $condition_image_cart);
                                $data_imagec = $dbComObj->fetch_object($result_imagec);

                                if (isset($data_imagec->thumb_image))
                                    $thumb_image_cart =   'images/products/thumb/' . $data_imagec->thumb_image;
                                else
                                    $thumb_image_cart =  'frontend/img/default-product-image.png';
                                // end of image code
                                $productData = array();
                                if($data->seller_id) $seller_id=$ajGenObj->NameById($data->seller_id, 'users');else $seller_id=''; 
                                $productData['productId'] = $data->product_id;
                                $productData['sellerId'] = $seller_id;
                                ;
                                $productData['slug'] = $data->slug;
                                $productData['sku_product'] = $data->SKU;
                                $productData['Brand'] = $data->Brand;
                                //$productData['slug'] = $data->product_id; 
                                $productData['productName'] = $data->product_name;
                                $productData['productImage'] = $thumb_image_cart;
                                $productData['price'] = ($data->price);
                                $productData['discount'] = $data->discount;
                                //  $productData['qty'] = $data->product_id;
                                $productData1 = json_encode($productData);
                                //print_r($productData1);
                                ?>
                                     <div id ="grouupedAJproducts"> 
            <?php

            if ($group_products_arr) {
               echo '<div class="full-width margintop30 postion-rl border-bottom">
                        <h4 class="hadding text-left"> Group products </h4>';
                    $ir = 0;
                    $wrap_countrg = 8; // you can change this no of divs to wrap
                    if (count($group_products_arr) > $wrap_countrgs) {
                                for ($x = 0; $x < count($group_products_arr); $x++) {
                                    //echo "The number is: $related_arr[$x] <br>";
                                    $condition_grp = "`product_id` = " . $group_products_arr[$x] . " ";
                                    $result_grp = $dbComObj->viewData($conn, "products", "*", $condition_grp);
                                    $data_grp = $dbComObj->fetch_object($result_grp);
                                    $url_id = $njEncryptionObj->safe_b64encode($data_grp->product_id);
                                    $datail_page1 = BASE_URL . 'detail/' . $url_id . '/' . $data_grp->slug . '/';
                                    $ir+=1;
                                    if ($data_grp->price)
                                        $data_price_grp = $currency_symbol . number_format($data_grp->price, 2);
                                    else
                                        $data_price_grp = '0.00';
                                    // image code start
                                    $condition_image11 = "`product_id` = " . $group_products_arr[$x] . " ORDER BY RAND()  LIMIT 1";
                                    $result_image11 = $dbComObj->viewData($conn, "product_image", "*", $condition_image11);
                                    $data_image11 = $dbComObj->fetch_object($result_image11);

                                    if (isset($data_image1->thumb_image))
                                        $thumb_image1 = BASE_URL . 'images/products/thumb/' . $data_image11->thumb_image;
                                    else
                                        $thumb_image1 = BASE_URL . 'frontend/img/default-product-image.png';
                                    // end of image code
                                    if ($ir % $wrap_countrg == 1) {
                                        ?>
                                        <div class="item <?php if ($ir < 4) echo "active"; ?>"><div class="row">
                                            <?php }
                                            ?>
                                            <div class="col-sm-4">
                                                <div class="col-item">
                                                    <div class="photo">
                                                        <img src="<?php echo $thumb_image1; ?>" class="img-responsive" alt="a" />
                                                    </div>
                                                    <div class="info">
                                                     <a href="<?php echo $datail_page1; ?>">
                                                            <div class="row">
                                                                <div class="price col-md-12 text-center">
                                                                    <h5>
        <?php echo $data_grp->product_name; ?></h5>

                                                                </div>
                                                                <div class=" text-center full-width">
                                                                    <i class="price-text-color fa fa-star"></i><i class="price-text-color fa fa-star">
                                                                    </i><i class="price-text-color fa fa-star"></i><i class="price-text-color fa fa-star">
                                                                    </i><i class="fa fa-star"></i>
                                                                </div>
                                                                <div class="col-md-12 text-center">
                                                                    <h5 class="price-text-color">
                                                                <?php echo $data_price_grp; ?></h5>
                                                                 <h5 class="price-text-color">
                                                                 <?php
                                                                    if ($data_grp->is_in_stock == '1')
                                                                        echo ' In Stock';
                                                                    else
                                                                        echo 'Out of Stock';
                                                                    ?></h5>
         
                                                                </div>
                                                            </div></a>

                                                        <div class="clearfix">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>



                                            <?php
                                            if ($ir % $wrap_countrg == 0) {
                                                echo '</div></div>';
                                            }
                                        }
                                        if ($ir % $wrap_countrg != 0) {
                                            echo '</div></div>';
                                        }
                                }
                                echo "</div>";
                          }
                    
                    
                    ?>
                        </div>
                        </div>
                        </div>

                    <div class="full-width">
                        <div class="col-md-4 col-sm-4">
                            <div class="row">

                                <h4 class="hadding text-left">QTY</h4>
                                <div class="input-group spinner">
                                    <input type="number" min="0" max="99" class="form-control" id="cart_qty" name="qty" value="1">

                                    <div class="input-group-btn-vertical">
                                        <button class="btn btn-success" type="button"><i class="fa fa-caret-up"></i></button>
                                        <button class="btn btn-success" type="button"><i class="fa fa-caret-down"></i></button>
                                    </div>
                                </div>

                                <script>
                                    (function ($) {
                                        $('.spinner .btn:first-of-type').on('click', function () {
                                            $('.spinner input').val(parseInt($('.spinner input').val(), 10) + 1);
                                        });
                                        $('.spinner .btn:last-of-type').on('click', function () {
                                            $('.spinner input').val(parseInt($('.spinner input').val(), 10) - 1);
                                        });
                                    })(jQuery);
                                </script>
                            </div>
                        </div>
                        <br> 
                        <div class="custom_options_container">
                            <?php
//custom option sitart
                            $condition_opt = "1 and product_id='" . $id . "' ";
                            $result_opt = $dbComObj->viewData($conn, "custom_options", "*", $condition_opt);
                            $num_opt = $dbComObj->num_rows($result_opt);
                            $co_count = 1;
                            if ($num_opt > 0) {
                                while ($data_opt = $dbComObj->fetch_assoc($result_opt)) {
                                    ?>        
                                    <div class="form-group col-md-4">
                                        <label for="sel1">Select <?php echo $data_opt['title']; ?> <?php
                                            $data_opt['is_require'] == 1;
                                            echo '*';
                                            ?>:</label>
                                        <select  class="form-control custom_options" id="custom_<?php echo $co_count; ?>" name="custom_option_<?php echo $data_opt['title']; ?>" <?php
                                        $data_opt['is_require'] == 1;
                                        echo 'required';
                                        ?> >
                                            <option value='' data-price='0'> select <?php echo $data_opt['title']; ?></option>
                                            <?php
                                            $conditionv = "1 and option_id=" . $data_opt['id'] . " ";
                                            $resultv = $dbComObj->viewData($conn, "custom_option_value", "*", $conditionv);
                                            $numv = $dbComObj->num_rows($resultv);
                                            if ($numv > 0) {
                                                while ($datav = $dbComObj->fetch_assoc($resultv)) {
                                                    if ($datav['price']) {
                                                        $data_custom_price = $datav['price'];
                                                    } else {
                                                        $data_custom_price = 0;
                                                    }
                                                    ?> 
                                                    <option value="<?php echo $datav['id']; ?>" data-title='<?php echo $datav['option_title']; ?>' data-price="<?php
                                                    if ($datav['price'])
                                                        echo $datav['price'];
                                                    else
                                                        echo 0;
                                                    ?>" ><?php echo $datav['option_title']; ?> + 
                                                    <?php echo $currency_symbol . number_format($data_custom_price, 2); ?> </option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <?php
                                    $co_count++;
                                }
                            }
                            // end of custom option  
                            ?>

                        </div>
                        <div class="col-md-4 col-sm-4">
                            <h4 class="hadding text-left"></h4>
                            <!--  <div class="input-field margintop25">
                       <select class="form-control">
                         <option value="" disabled selected>Choose your QTY</option>
                         <option value="1">1</option>
                         <option value="2">2</option>
                         <option value="3">3</option>
                       </select>
                     </div> -->
                        </div>    
                    </div>
                    <br>     <br>
                        <div class="col-md-8 col-sm-6 col-xs-8">
                                <a  onclick="addToCart('<?php echo $data->product_id; ?>');" class="btn btn-success margintop10 btn-block"><i class="fa fa-shopping-cart"></i> Add to Cart</a>
                           </div>      
                            
                        
                        <div class="col-md-4 col-sm-4 col-xs-2">
                            <a onclick="addWishlist('<?php echo $data->product_id; ?>')" class="circel margintop4"><i class="fa fa-heart"></i></i></a>
                        </div>
                    </div>
                    <div class="full-width margintop20">
                        <div class="col-md-12 col-sm-12">
                            <div class="row">
                                <div class="">
                                    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                                        <div class="panel panel-default">
                                            <div class="panel-heading" role="tab" id="headingOne">
                                                <h4 class="panel-title">

                                                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                        Product Details <span class="glyphicon glyphicon-plus pull-right"></span>

                                                    </a>
                                                </h4>
                                            </div>
                                            <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                                                <div class="panel-body">
                                                    <p><?php echo base64_decode($data->product_description); ?></p>
                                                     <!-- <p><a href="javascript:void(0)">Circumference:2.4"</a></p>
                                                     <p><a href="javascript:void(0)">Adjustable from:2.2" to 2.6"</a></p>
                                                     <p><a href="javascript:void(0)">Made with .925 sterling silver</a></p>
                                                     <p><a href="javascript:void(0)">Polished for a shiny finish</a></p> -->
<?php if (isset($data->SKU) && $data->SKU) { ?>
                                                        <p><a href="javascript:void(0)">
                                                                SKU:<?php echo $data->SKU; ?>
                                                            </a></p> <?php } ?>

<?php if (isset($data->Brand) && $data->Brand) { ?>
                                                        <p><a href="javascript:void(0)">
                                                                Brand:<?php echo $ajGenObj->NameById($data->Brand, 'brands'); // echo $data->Brand;   ?>
                                                            </a></p> <?php } ?>

<?php if (isset($data->carat) && $data->carat) { ?>
                                                        <p><a href="javascript:void(0)">
                                                                Carat:<?php echo $data->carat; ?>
                                                            </a></p> <?php } ?>

<?php if (isset($data->measurement_size) && $data->measurement_size) { ?>
                                                        <p><a href="javascript:void(0)">
                                                                Measurement size:<?php echo $data->measurement_size; ?>
                                                            </a></p> <?php } ?>

<?php if (isset($data->Material) && $data->Material) { ?>
                                                        <p><a href="javascript:void(0)">
                                                                Material:<?php echo $data->Material; ?>
                                                            </a></p> <?php } ?>

<?php if (isset($data->product_metal) && $data->product_metal) { ?>
                                                        <p><a href="javascript:void(0)">
                                                                Metal:<?php echo $data->product_metal; ?>
                                                            </a></p> <?php } ?>

<?php if (isset($data->unit_weight) && $data->unit_weight) { ?>
                                                        <p><a href="javascript:void(0)">
                                                                Unit weight:<?php echo $data->unit_weight; ?>
                                                            </a></p> <?php } ?>

<?php if (isset($data->stone) && $data->stone) { ?>
                                                        <p><a href="javascript:void(0)">
                                                                Stone:<?php echo $data->stone; ?>
                                                            </a></p> <?php } ?>

<?php if (isset($data->stone_description) && $data->stone_description) { ?>
                                                        <p><a href="javascript:void(0)">
                                                                Stone description:<?php echo base64_decode($data->stone_description); ?>
                                                            </a></p> <?php } ?>

<?php if (isset($data->no_of_stone) && $data->no_of_stone) { ?>
                                                        <p><a href="javascript:void(0)">
                                                                No of stone:<?php echo $data->no_of_stone; ?>
                                                            </a></p> <?php } ?>

<?php if (isset($data->stone_setting) && $data->stone_setting) { ?>
                                                        <p><a href="javascript:void(0)">
                                                                Stone setting:<?php echo $data->stone_setting; ?>
                                                            </a></p> <?php } ?>

<?php if (isset($data->stone_color) && $data->stone_color) { ?>
                                                        <p><a href="javascript:void(0)">
                                                                Stone color:<?php echo $data->stone_color; ?>
                                                            </a></p> <?php } ?>


<?php if (isset($data->stone_clarity) && $data->stone_clarity) { ?>
                                                        <p><a href="javascript:void(0)">
                                                                Stone clarity:<?php echo $data->stone_clarity; ?>
                                                            </a></p> <?php } ?>

<?php if (isset($data->stone_shape) && $data->stone_shape) { ?>
                                                        <p><a href="javascript:void(0)">
                                                                Stone shape:<?php echo $data->stone_shape; ?>
                                                            </a></p> <?php } ?>

<?php if (isset($data->attribute_set_id) && $data->attribute_set_id) { ?>
                                                        <p><a href="javascript:void(0)">
                                                                Attribute set:<?php echo $ajGenObj->NameById($data->attribute_set_id, 'attribute_set'); // echo $data->Brand;   ?>
                                                            </a></p> <?php } ?>
<?php if (isset($data->attribute_opt_value) && $data->attribute_opt_value) { ?>
                                                        <p><a href="javascript:void(0)">

                                                                Attribute: <span class="attribute_value"><?php
                                                                    $attribute_opt_value_arr = json_decode($data->attribute_opt_value);

                                                                    foreach ($attribute_opt_value_arr as $key => $value) {
                                                                        echo $key . ':' . $value . '<br>';
                                                                    }
                                                                    ?>
                                                                </span> </a></p> <?php } ?>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel panel-default">
                                            <div class="panel-heading" role="tab" id="headingTwo">
                                                <h4 class="panel-title">
                                                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                        Shiping & Return <span class="glyphicon glyphicon-plus pull-right"></span>
                                                    </a>
                                                </h4>
                                            </div>
                                            <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                                                <div class="panel-body">
                                                    <p>Love is the Greatest virtue of all. It is at the root of all good things. All humans thrive in a loving environments. And whenever love is in the equation, happiness results.</p>
                                                    <p>The Perfect addition to your Charmed fingers </p>
                                                    <p><a href="#">Circumference:2.4"</a></p>
                                                    <p><a href="#">Adjustable from:2.2" to 2.6"</a></p>
                                                    <p><a href="#">Made with .925 sterling silver</a></p>
                                                    <p><a href="#">Polished for a shiny finish</a></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel panel-default">
                                            <div class="panel-heading" role="tab" id="headingThree">
                                                <h4 class="panel-title">
                                                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                                        Product Care <span class="glyphicon glyphicon-plus pull-right"></span>
                                                    </a>
                                                </h4>
                                            </div>
                                            <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
                                                <div class="panel-body">
                                                    <p>Love is the Greatest virtue of all. It is at the root of all good things. All humans thrive in a loving environments. And whenever love is in the equation, happiness results.</p>
                                                    <p>The Perfect addition to your Charmed fingers </p>
                                                    <p><a href="#">Circumference:2.4"</a></p>
                                                    <p><a href="#">Adjustable from:2.2" to 2.6"</a></p>
                                                    <p><a href="#">Made with .925 sterling silver</a></p>
                                                    <p><a href="#">Polished for a shiny finish</a></p>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="full-width">
                        <div class="col-md-8 col-sm-8">
                            <p>Contact US: <strong><a href="#"><i class="fa fa-envelope marginleft30"></i></a>  <i class="fa fa-phone marginleft30"></i> Order by phone </strong><br> <a href="#" class="marginleft38persent">+91-000-000-0000</a></p>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <ul class="social">
                                <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                                <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                                <li><a href="#"><i class="fa fa-google-plus"></i></a></li>
                                <li><a href="#"><i class="fa fa-pinterest-p"></i></a></li>
                            </ul>
                        </div>
                    </div>

                </div>
            </div>
               <div id ="grouupedAJproducts"> 
            <?php

            if ($group_products_arr) {
               echo '<div class="full-width margintop30 postion-rl border-bottom">
                        <h4 class="hadding text-left"> Group products </h4>';
                    $ir = 0;
                    $wrap_countrg = 8; // you can change this no of divs to wrap
                    if (count($group_products_arr) > $wrap_countrgs) {
                                for ($x = 0; $x < count($group_products_arr); $x++) {
                                    //echo "The number is: $related_arr[$x] <br>";
                                    $condition_grp = "`product_id` = " . $group_products_arr[$x] . " ";
                                    $result_grp = $dbComObj->viewData($conn, "products", "*", $condition_grp);
                                    $data_grp = $dbComObj->fetch_object($result_grp);
                                    $url_id = $njEncryptionObj->safe_b64encode($data_grp->product_id);
                                    $datail_page1 = BASE_URL . 'detail/' . $url_id . '/' . $data_rel->slug . '/';
                                    $ir+=1;
                                    if ($data_grp->price)
                                        $data_price_grp = $currency_symbol . number_format($data_grp->price, 2);
                                    else
                                        $data_price_grp = '0.00';
                                    // image code start
                                    $condition_image1 = "`product_id` = " . $group_products_arr[$x] . " ORDER BY RAND()  LIMIT 1";
                                    $result_image1 = $dbComObj->viewData($conn, "product_image", "*", $condition_image1);
                                    $data_image1 = $dbComObj->fetch_object($result_image1);

                                    if (isset($data_image1->thumb_image))
                                        $thumb_image1 = BASE_URL . 'images/products/thumb/' . $data_image1->thumb_image;
                                    else
                                        $thumb_image1 = BASE_URL . 'frontend/img/default-product-image.png';
                                    // end of image code
                                    if ($ir % $wrap_countrg == 1) {
                                        ?>
                                        <div class="item <?php if ($ir < 4) echo "active"; ?>"><div class="row">
                                            <?php }
                                            ?>
                                            <div class="col-sm-3">
                                                <div class="col-item">
                                                    <div class="photo">
                                                        <img src="<?php echo $thumb_image1; ?>" class="img-responsive" alt="a" />
                                                    </div>
                                                    <div class="info"> <a href="<?php echo $datail_page1; ?>">
                                                            <div class="row">
                                                                <div class="price col-md-12 text-center">
                                                                    <h5>
        <?php echo $data_grp->product_name; ?></h5>

                                                                </div>
                                                                <div class=" text-center full-width">
                                                                    <i class="price-text-color fa fa-star"></i><i class="price-text-color fa fa-star">
                                                                    </i><i class="price-text-color fa fa-star"></i><i class="price-text-color fa fa-star">
                                                                    </i><i class="fa fa-star"></i>
                                                                </div>
                                                                <div class="col-md-12 text-center">
                                                                    <h5 class="price-text-color">
        <?php echo $data_price_grp; ?></h5>
                                                                </div>
                                                            </div></a>

                                                        <div class="clearfix">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>



                                            <?php
                                            if ($ir % $wrap_countrg == 0) {
                                                echo '</div></div>';
                                            }
                                        }
                                        if ($ir % $wrap_countrg != 0) {
                                            echo '</div></div>';
                                        }
			                    }
                                echo "</div>";
                          }
					
                    
                    ?>
                        </div>
            <div id ="related_check_list">
            <?php
            $related_check_list = $data->related_check_list;
            if ($related_check_list) {
                ?>
                <div class="full-width margintop30 postion-rl">
                    <h4 class="hadding">You Might Also Like</h4>
                    <?php
                    $related_arr = explode(',', $related_check_list);
                    $ir = 0;
                    $wrap_countr = 4; // you can change this no of divs to wrap
                    if (count($related_arr) > $wrap_countr) {
                        ?>

                        <a data-slide="prev" href="#carousel-example" class="left carousel-control"><i class="fa fa-angle-left"></i></a>
                        <a data-slide="next" href="#carousel-example" class="right carousel-control"><i class="fa fa-angle-right"></i></a>
    <?php } ?>

                    <div id="carousel-example" class="carousel slide full-width margintop10" data-ride="carousel">
                        <!-- Wrapper for slides -->
                        <div class="col-md-10 col-sm-10 col-md-offset-1">
                            <div class="carousel-inner ">
                                <?php
                                for ($x = 0; $x < count($related_arr); $x++) {
                                    //echo "The number is: $related_arr[$x] <br>";
                                    $condition_rel = "`product_id` = " . $related_arr[$x] . " ";
                                    $result_rel = $dbComObj->viewData($conn, "products", "*", $condition_rel);
                                    $data_rel = $dbComObj->fetch_object($result_rel);
                                    $url_id = $njEncryptionObj->safe_b64encode($data_rel->product_id);
                                    $datail_page1 = BASE_URL . 'detail/' . $url_id . '/' . $data_rel->slug . '/';
                                    $ir+=1;
                                    if ($data->price)
                                        $data_price_rel = $currency_symbol . number_format($data_rel->price, 2);
                                    else
                                        $data_price = '-';
                                    // image code start
                                    $condition_image1 = "`product_id` = " . $related_arr[$x] . " ORDER BY RAND()  LIMIT 1";
                                    $result_image1 = $dbComObj->viewData($conn, "product_image", "*", $condition_image1);
                                    $data_image1 = $dbComObj->fetch_object($result_image1);

                                    if (isset($data_image1->thumb_image))
                                        $thumb_image1 = BASE_URL . 'images/products/thumb/' . $data_image1->thumb_image;
                                    else
                                        $thumb_image1 = BASE_URL . 'frontend/img/default-product-image.png';
                                    // end of image code
                                    if ($ir % $wrap_countr == 1) {
                                        ?>
                                        <div class="item <?php if ($ir < 4) echo "active"; ?>"><div class="row">
                                            <?php }
                                            ?>
                                            <div class="col-sm-3">
                                                <div class="col-item">
                                                    <div class="photo">
                                                        <img src="<?php echo $thumb_image1; ?>" class="img-responsive" alt="a" />
                                                    </div>
                                                    <div class="info"> <a href="<?php echo $datail_page1; ?>">
                                                            <div class="row">
                                                                <div class="price col-md-12 text-center">
                                                                    <h5>
        <?php echo $data_rel->product_name; ?></h5>

                                                                </div>
                                                                <div class=" text-center full-width">
                                                                    <i class="price-text-color fa fa-star"></i><i class="price-text-color fa fa-star">
                                                                    </i><i class="price-text-color fa fa-star"></i><i class="price-text-color fa fa-star">
                                                                    </i><i class="fa fa-star"></i>
                                                                </div>
                                                                <div class="col-md-12 text-center">
                                                                    <h5 class="price-text-color">
        <?php echo $data_price_rel; ?></h5>
                                                                </div>
                                                            </div></a>

                                                        <div class="clearfix">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>



                                            <?php
                                            if ($ir % $wrap_countr == 0) {
                                                echo '</div></div>';
                                            }
                                        }
                                        if ($ir % $wrap_countr != 0) {
                                            echo '</div></div>';
                                        }
                                        ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php }
                    ?>
                        </div>
                    <div class="full-width margintop30 postion-rl border-bottom">
                        <h4 class="hadding text-left"> Customer questions & answers </h4>
                        <div class="col-md-6 col-sm-6 margintop20">
                            <input type="text" class="form-control" placeholder="Have a question? Search for answers" name="">
                            <a href="#" class="postion-abso"><i class="fa fa-search"></i></a>
                            <p class="margintop10 full-width">Typical questions asked about products: </p>
                            <p> -  Is the item durable? </p>
                            <p> -  Is this item easy to use? </p>
                            <p>-  What are the dimensions of this item? </p>
                        </div>

                    </div>
                    <div class="full-width margintop30">
                        <?php
                        $condition_rew = "1 and product_id = '" . $id . "'";
                        $result_rew = $dbComObj->viewData($conn, "reviews", "*", $condition_rew);
                        $num_rew = $dbComObj->num_rows($result_rew);
                        ?>
                        <h4 class="hadding text-left">Reviews(<?php echo $num_rew; ?>)</h4>
                        <?php
                        // echo  $_SERVER['REQUEST_URI'];  
                        if (empty($login_id)) {
                            $reference_page = 'detail/' . $UriSegmentID . '/' . $ajGenObj->getUriSegment(4) . '';
                            ?> <!-- ?model=writeReview-->
                            <a href="<?php echo BASE_URL; ?>login/?reference=<?php echo $reference_page ?>"
                               class="btn btn-success margintop30"> 
                                Write a Review </a>
                        <?php } else { ?>
                            <button type="button" class="btn btn-success margintop3" data-toggle="modal" data-target="#ajWriteReviewModal">Write a Review</button>  
<?php } ?>  
                        </br>
                        <div class="col-md-6 col-sm-6 margintop20">

                            <div id="getReviews"> </div>
                            <?php if ($num_rew > 10) { ?>
                                <button type="button" class="btn btn-success"  id="loadmoreReviews" >Load More</button>
<?php } ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal start-->
<div id="ajWriteReviewModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Write your own review</h4>
            </div>  
            <div class="modal-body">
<?php if (empty($login_id)) { ?>
                    <div class="alert alert-info">
                        <strong>info! </strong> Only registered users can write reviews
                    </div>
<?php } ?>
                <div id="errorMessageReviewLog"></div>
                <form method="post" id="addReviews" class="">  
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Title</label>
                                <input class="form-control" required="" type="text" id="title" name="title">
                            </div>
                            <div class="form-group">
                                <label>Review</label>
                                <textarea class="form-control" required="" name="description" id="description" rows="3"></textarea>
                            </div>

                            <div class="form-group">
                                <label class="custom-file">
                                    <input type="file" id="file" name="image" class="custom-file-input">
                                    <span class="custom-file-control"></span>
                                </label>
                            </div>
                            <div class="form-group">
                                <input type="hidden" name="mode" value="<?php echo base64_encode("addReview"); ?>" />
                                <input type="hidden" name="product_id" value="<?php echo $id; ?>" />
                                <button type="button" onclick="formSubmit('addReviews', 'errorMessageReviewLog', '<?php echo BASE_URL; ?>controller/review_operation.php')" class="btn btn-success margintop30 btn btn-lg">Post </button>

                            </div>
                        </div> 
                        <div class="col-md-4 form-group" >
                            Rate it
                            <div class="stars">
                                <input type="radio" name="rating" class="star-1" id="star-1" value="1" required=""/>
                                <label class="star-1" for="star-1">1</label>
                                <input type="radio" name="rating" class="star-2" id="star-2" value="2" required="" />
                                <label class="star-2" for="star-2">2</label>
                                <input type="radio" name="rating" class="star-3" id="star-3" value="3" required=""  />
                                <label class="star-3" for="star-3">3</label>
                                <input type="radio" name="rating" class="star-4" id="star-4" value="4"  required=""/>
                                <label class="star-4" for="star-4">4</label>
                                <input type="radio" name="rating" class="star-5" id="star-5" value="5" required="" />
                                <label class="star-5" for="star-5">5</label>
                                <span></span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>
<input type="hidden" class="reviewpageNum" name="reviewpageNum" value= "1"/>
<!-- end model write reviews-->
<?php if (isset($_GET['model']) && $_GET['model']) { ?>
    <script type="text/javascript">
        $(window).on('load', function () {
            //  alert(2);
            $('#ajWriteReviewModal').modal('show');

        });
    </script>
<?php } ?>
<script>
    $("#loadmoreReviews").show();
    loadReviews(1);

    $("#loadmoreReviews").click(function () {
        var pagenum = parseInt($(".reviewpageNum").val()) + 1;
        var datat = $(".reviewpageNum").val(pagenum);
        // alert(pagenum);
        loadReviews(pagenum);
    });
    function loadReviews(pagenum) {
        var mode = 'getReviews';
        var a = "<?php echo $UriSegmentID; ?>";
        $.ajax({
            url: "<?php echo BASE_URL; ?>getData.php",
            type: "POST",
            data: {page: pagenum, mode: mode, a: a},
            beforeSend: function () {
                $('#loading-overlay').show();
            },
            complete: function () {
                $('#loading-overlay').hide();
            },
            success: function (data) {
                //  alert(data);
                // alert(viewtype);
                if (data) {
                    $("#getReviews").append(data);
                    $("#loadmoreReviews").show();
                } else {
                    $("#loadmoreReviews").hide();
                }
                $('#loading-overlay').hide();
            },
            error: function () {
            }
        });
    }
</script>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>frontend/custom/star.css" type="text/css" media="all" />
<script>
     function serverResponse(id,res,alertClass,tm="8000") {
          
         var resmsg1 ='<div class="alert alert-dismissable alert alert-'+alertClass+'"><a href="#" class="close" data-dismiss="alert" aria-label="close"> x </a>\<strong class="text-capitalize">'+alertClass+'!</strong> <span>'+res+'</span> </div>';
        //response  msg aj st
                  $('#'+id).show();
                  if(alertClass=="success"){
                        $('#'+id).html(resmsg1);
                                window.setTimeout(function () {
                                 //   $('#'+id).fadeTo(500, 0).slideUp(500, function () {
                                          $('#'+id).html("");
                                 //   });
                                }, tm);
                    }
}
    function addWishlist(id) {
        $('#loading-overlay').show();
        var user_id = '<?php echo $login_id; ?>';
        if (user_id) {
            $.ajax({
                type: "POST",
                data: {product_id: id, mode: 'addWishlist'},
                url: "<?php echo BASE_URL ?>/controller/wishlist_operation.php",
                success: function (data) {
                    if (data) {
                        //  alert();
                      serverResponse('resajmsg',data,"success")
                        $('#loading-overlay').hide();
                        //response  msg aj end     
                    }
                    else {
                        alert('Error occured');
                    }
                }
            });
        } else {
            // alert("please login to add  products to wishlist");
            //response  msg aj st
          var response ='please login to add  products to wishlist';
                 serverResponse('resajmsg',response,"success"); //success danger //info
            //response  msg aj end     
        }
    }
    function addWishlistaaaaa(a) {
        var user_id = '<?php echo $login_id; ?>';
        var productData = <?php echo $productData1; ?>;
        $.post('<?php echo BASE_URL; ?>controller/cart_operations.php', {productId: a, productData: productData, mode: '<?php echo base64_encode('setWishCart'); ?>'}, function (response) {
            alert(response);
        });
    }
    function addToCart1(id) {

        var uid = '<?php echo base64_encode($login_id); ?>';
        var qty = $("#cart_qty").val();
        var mode = '<?php echo base64_encode('addToCart'); ?>';
        //   alert(qty);

        var custom_id1 = $('#custom_1').val();
        var custom_price1 = parseFloat($('#custom_1').find(':selected').attr('data-price'));
        var custom_title1 = ($('#custom_1').find(':selected').attr('data-title'));

        var custom_id2 = $('#custom_2').val();
        var custom_price2 = parseFloat($('#custom_2').find(':selected').attr('data-price'));
        var custom_title2 = ($('#custom_2').find(':selected').attr('data-title'));

        var custom_id3 = $('#custom_3').val();
        var custom_price3 = parseFloat($('#custom_3').find(':selected').attr('data-price'));
        var custom_title3 = ($('#custom_3').find(':selected').attr('data-title'));

        var datastring = {custom_id1: custom_id1, custom_price1: custom_price1, custom_title1: custom_title1,
            custom_id2: custom_id2, custom_price2: custom_price2, custom_title2: custom_title2,
            custom_id3: custom_id3, custom_price3: custom_price3, custom_title3: custom_title3,
            product_id: id, mode: mode, qty: qty
        }
        if (qty) {
        } else {
            alert("please select quantity");

        }

        if (uid) {
            $.ajax({
                type: "POST",
                data: datastring,
                url: "<?php echo BASE_URL ?>/controller/cart_operation.php",
                success: function (data) {
                    alert(data);

                }
            });
        } else {
            alert("please login to add  products to cart");
        }
    }
    ////custom option ////////
    $(".custom_options").change(function () {
        $(this).val();
        var product_price = parseFloat('<?php if ($data->price) echo $data->price; ?>');
       // var product_price = parseFloat($('#product_price').text());
        var select_price = parseFloat($(this).find(':selected').attr('data-price'));
        //alert(product_price  );  alert(select_price  );
        product_price_new = '';
        product_price_new = product_price + select_price;
        $('#product_price').text(product_price_new.toFixed(2));
    });
    /////add to cart cookies
    function addToCart(a)
    {
               //    alert(a);
        var uid = '<?php echo base64_encode($login_id); ?>';
        var qty = $("#cart_qty").val();

        //   alert(qty);

        var custom_id1 = $('#custom_1').val();
        var custom_price1 = parseFloat($('#custom_1').find(':selected').attr('data-price'));
        var custom_title1 = ($('#custom_1').find(':selected').attr('data-title'));

        var custom_id2 = $('#custom_2').val();
        var custom_price2 = parseFloat($('#custom_2').find(':selected').attr('data-price'));
        var custom_title2 = ($('#custom_2').find(':selected').attr('data-title'));

        var custom_id3 = $('#custom_3').val();
        var custom_price3 = parseFloat($('#custom_3').find(':selected').attr('data-price'));
        var custom_title3 = ($('#custom_3').find(':selected').attr('data-title'));

        var jsondata2 = {custom_id1: custom_id1, custom_price1: custom_price1, custom_title1: custom_title1,
            custom_id2: custom_id2, custom_price2: custom_price2, custom_title2: custom_title2,
            custom_id3: custom_id3, custom_price3: custom_price3, custom_title3: custom_title3,
            product_id: a, qty: qty
        }

        if (qty && qty > 0) {
        } else {
          //  alert(qty);
            // alert("please select quantity");
            //response  msg aj st
            // $('#resajmsg').text("");
               var response = 'please select quantity';
               serverResponse('resajmsg',response,"danger");
                return true;
            //response  msg aj end     
        }

        $('#loading-overlay').show();
        //var productData = jQuery.parseJSON(<?php echo $productData1; ?>);
        // alert(productData);
        var jsondata1 = <?php echo $productData1; ?>;
        var productData = $.extend({}, jsondata1, jsondata2);
        // console.log(productData);
        //alert(productData);    
        $.post('<?php echo BASE_URL; ?>controller/cart_operations.php', {productId: a, productData: productData, mode: '<?php echo base64_encode('setCart'); ?>'}, function (response) {
             //alert(response);
             serverResponse('resajmsg',response,"success"); //success danger //info
            //response  msg aj end     

            $('#loading-overlay').hide();
            // $('#manageCart_Nj').load("<?php echo BASE_URL; ?>/controller/manage_cart.php");
            return false;
        });
        return false;
    }

</script>

<?php include 'include/footer.php'; ?>
