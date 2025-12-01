@extends('layouts.app')

@section('title', 'Catalogue des bouteilles')
@section('content')
<section class="p-4" role="section-catalogue-bouteilles">
<x-page-header title="Catalogue des bouteilles" />
<x-search-filter :pays="$pays" :types="$types" :millesimes="$millesimes" />
 <div id="catalogueContainer">
        @include('bouteilles._catalogue_list', ['bouteilles' => $bouteilles])

</div>
</section>
<x-modal-pick-cellar />
@endsection