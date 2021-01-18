<div class="top-content mb-2">
	@php($customIndexTitle = setting('icommerce::customIndexTitle'))
	<h1 class="text-primary text-uppercase font-weight-bold h3">{{$category->title ?? (!empty($customIndexTitle) ? setting('icommerce::customIndexTitle') : trans('icommerce::products.title.products') )}} {{isset($manufacturer->id) ? isset($category->id) ? "/ $manufacturer->name" : $manufacturer->name : ""}}</h1>
	@if(isset($category->options->descriptionIndex) && !empty($category->options->descriptionIndex))
		<p class="category-index-description">
			{{$category->options->descriptionIndex}}
		</p>
		@endif
	<hr>

	<div class="row align-items-center">

		{{-- Total Products --}}
		<div class="col-lg-4">

			<div class="total-products">
				<label class="title mb-0">
					{{trans('icommerce::frontend.index.we found')}}:
				</label>
				<label class="infor mb-0">
					{{$totalProducts}}
					{{trans('icommerce::products.plural')}}
				</label>
			</div>
			
		</div>

		{{-- Filter - Order By --}}
		<div class="col-lg-5">
			@include('icommerce::frontend.livewire.index.top-content.filter-orderby')
		</div>

		{{-- Change Layout --}}
		<div class="col-lg-3">
			@include('icommerce::frontend.livewire.index.top-content.change-layout') 
		</div>
		
	</div>

</div>