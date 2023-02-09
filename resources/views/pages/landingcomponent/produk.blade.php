<!-- NUMBERS -->
<div id="numbers" class="section">
    <!-- container -->
    <div class="container">
        <!-- row -->
        <div id="div-icon" class="row" style="height: 370px; overflow: hidden;">

            <!-- section title -->
            <div class="col-md-12">
                <div class="section-title">
                    <h2 class="title" style="font-family: 'Century Gothic';"><span style="color: #006cb8; font-weight: bold;">PRODUK</span> BPAD</h2>
                    <!-- <p class="sub-title">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p> -->
                </div>
            </div>
            <!-- section title -->

            @for($i=0; $i < count($produk_content); $i++)

            <!-- number -->
            <div class="col-md-4 col-sm-6">
                <div class="number">
                    <a target="_blank" href="{{ $produk_content[$i]['href'] }}">
                        <img src="{{ str_replace('public/', '', $produk_content[$i]['source']) }}" alt="{{ $produk_content[$i]['name'] }}" width="150" class="static">

                        <?php $i++; ?>

                        <img src="{{ str_replace('public/', '', $produk_content[$i]['source']) }}" alt="{{ $produk_content[$i]['name'] }}" width="150" class="active">
                    </a>
                    <!-- <i class="fa fa-smile-o"></i> -->
                    <h4>{{ $produk_content[$i]['name'] }}</h4>
                    <!-- <span>eDokumen</span> -->
                </div>
            </div>
            <!-- /number -->

            @endfor
        </div>
        <!-- /row -->
        <div class="text-center">
            <a href="JavaScript:void(0);" class="primary-button show-icon">Lihat Semua</a>
        </div>
    </div>
    <!-- /container -->
</div>
<!-- /NUMBERS -->