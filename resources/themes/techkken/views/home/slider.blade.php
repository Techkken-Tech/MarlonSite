@if (count($sliderData))
    <section id="megabanner" class="slider-block full-bleed">
        <image-slider :slides='@json($sliderData)' public_path="{{ url()->to('/') }}"></image-slider>
    </section>
@endif