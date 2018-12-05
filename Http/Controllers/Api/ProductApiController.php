<?php

namespace Modules\Icommerce\Http\Controllers\Api;

// Requests & Response
use Modules\Icommerce\Http\Requests\ProductRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

// Base Api
use Modules\Ihelpers\Http\Controllers\Api\BaseApiController;

// Transformers
use Modules\Icommerce\Transformers\ProductTransformer;

// Repositories
use Modules\Icommerce\Repositories\ProductRepository;

// Entities
use Modules\Icommerce\Entities\Tag;


class ProductApiController extends BaseApiController
{
  
  private $product;
  
  
  public function __construct(
    ProductRepository $product)
  {
    $this->product = $product;
  }
  
  /**
   * Display a listing of the resource.
   * @return Response
   */
  public function index()
  {
    try {
      //Get Parameters from URL.
      $p = $this->parametersUrl(false, false, ['status' => [1]], []);
      
      //Request to Repository
      $products = $this->product->index($p->page, $p->take, $p->filter, $p->include, $p->fields);
      
      //Response
      $response = ['data' => ProductTransformer::collection($products)];
      //If request pagination add meta-page
      $p->page ? $response['meta'] = ['page' => $this->pageTransformer($products)] : false;
      
    } catch (\Exception $e) {
      //Message Error
      $status = 400;
      $response = [
        'errors' => $e->getMessage()
      ];
    }
    return response()->json($response, $status ?? 200);
  }
  
  /** SHOW
   * @param Request $request
   *  URL GET:
   *  &fields = type string
   *  &include = type string
   */
  public function show($criteria, Request $request)
  {
    try {
      //Get Parameters from URL.
      $p = $this->parametersUrl(false, false, [], []);
      
      //Request to Repository
      $product = $this->product->show($p->filter, $p->include, $p->fields, $criteria);
      
      $response = [
        'data' => $product ? new ProductTransformer($product) : '',
      ];
      
    } catch (\Exception $e) {
      $status = 400;
      $response = [
        'errors' => $e->getMessage()
      ];
    }
    return response()->json($response, $status ?? 200);
  }
  
  /**
   * Show the form for creating a new resource.
   * @return Response
   */
  public function create(ProductRequest $request)
  {
    try {
      $product = $this->product->create($request->all());
  
      // sync tables
      if ($product) {
        // Categories
        if (isset($request->categories))
          $product->categories()->sync($request->categories);
    
        // Product Options
        if (isset($request->options))
          $product->options()->sync($request->options);
    
        // Product Option Values
        if (isset($request->optionValues))
          $product->optionValues()->sync($request->optionValues);
    
        // Related Products
        if (isset($request->relatedProducts))
          $product->relatedProducts()->sync($request->relatedProducts);
    
        // Discounts
        if (isset($request->discounts))
          $product->discounts()->sync($request->discounts);
    
        // Tags
        if (isset($request->tags)){
          $request->tags = $this->addTags($request->tags);
          $product->tags()->sync($request->tags);
        }
    
        // Image
        if (isset($request->mainimage) && !empty($request->mainimage)) {
          $mainimage = $this->saveImage($request->mainimage, "assets/icommerce/product/" . $product->id . ".jpg");
      
          $options = $product->options;
          $options["mainImage"] = $mainimage;
          $product->options = json_encode($options);
      
        }
    
        // File
        if ($request->hasFile('pfile') && $request->file('pfile')->isValid()) {
          $filePath = $this->saveFile($request->file('pfile'), $product->id);
          if ($filePath != null) {
            $options = (array)$product->options;
            $options["mainFile"] = $filePath;
            $product->options = json_encode($options);
        
          }
        }
    
        // Metas
        $this->saveMetas($request,$product);
    
        $product->save();
      }
      
      $response = ['data' => ''];
      
    } catch (\Exception $e) {
      $status = 400;
      $response = [
        'errors' => $e->getMessage()
      ];
    }
    return response()->json($response, $status ?? 200);
  }
  
  /**
   * Update the specified resource in storage.
   * @param  Request $request
   * @return Response
   */
  public function update($id, ProductRequest $request)
  {
    try {
      
      $product = $this->product->find($id);
      $product = $this->product->update($product, $request->all());
      
      // sync tables
      if ($product) {
        // Categories
        if (isset($request->categories))
          $product->categories()->sync($request->categories);
        
        // Product Options
        if (isset($request->options))
          $product->options()->sync($request->options);
        
        // Product Option Values
        if (isset($request->optionValues))
          $product->optionValues()->sync($request->optionValues);
        
        // Related Products
        if (isset($request->relatedProducts))
          $product->relatedProducts()->sync($request->relatedProducts);
        
        // Discounts
        if (isset($request->discounts))
          $product->discounts()->sync($request->discounts);
  
        // Tags
        if (isset($request->tags)){
          $request->tags = $this->addTags($request->tags);
          $product->tags()->sync($request->tags);
        }
  
        // Image
        if (isset($request->mainimage) && !empty($request->mainimage)) {
          $mainimage = $this->saveImage($request->mainimage, "assets/icommerce/product/" . $product->id . ".jpg");
    
          $options = $product->options;
          $options["mainImage"] = $mainimage;
          $product->options = json_encode($options);
    
        }
  
        // File
        if ($request->hasFile('pfile') && $request->file('pfile')->isValid()) {
          $filePath = $this->saveFile($request->file('pfile'), $product->id);
          if ($filePath != null) {
            $options = (array)$product->options;
            $options["mainFile"] = $filePath;
            $product->options = json_encode($options);
      
          }
        }
        
        // Metas
        $this->saveMetas($request,$product);
  
        $product->save();
      }
      
      $response = ['data' => ''];
      
    } catch (\Exception $e) {
      $status = 400;
      $response = [
        'errors' => $e->getMessage()
      ];
    }
    return response()->json($response, $status ?? 200);
  }
  
  
  /**
   * Remove the specified resource from storage.
   * @return Response
   */
  public function delete($id, Request $request)
  {
    try {
      $product = $this->product->find($id);
      $product->delete();
      
      $response = ['data' => ''];
      
    } catch (\Exception $e) {
      $status = 400;
      $response = [
        'errors' => $e->getMessage()
      ];
    }
    return response()->json($response, $status ?? 200);
  }
  
  /**
   * Add tags Product
   *
   * @param  Array tags ids
   * @return Array
   */
  public function addTags($tags)
  {
    
    $tags = $tags;
    $newtags = Array();
    $lasttagsid = Array();
    $newtagsid = Array();
    
    if (!empty($tags)) {
      
      //se recorren todos lostags en busca de alguno nuevo
      foreach ($tags as $tag) {
        //si el tag no existe se agrega al array de de nuevos tags
        if (count(Tag::find($tag)) <= 0) {
          array_push($newtags, $tag);
        } else {
          //si el tag existe se agrega en un array de viejos tags
          array_push($lasttagsid, $tag);
        }
      }
    }
    //se crean todos los tags que no existian
    foreach ($newtags as $newtag) {
      $modeltag = new Tag;
      $modeltag->title = $newtag;
      $modeltag->slug = str_slug($newtag, '-');
      $modeltag->save();
      
      array_push($newtagsid, $modeltag->id);
    }
    
    //se modifica el valor tags enviado desde el form uniendolos visjos tags y los tags nuevos
    return array_merge($lasttagsid, $newtagsid);
    
  }
  
  /**
   * Save Metas.
   *
   * @param  Request $request
   * @param  Object $product
   * @return nothing
   */
  public function saveMetas(Request $request, $product)
  {
    $options = (array)$product->options;
  
    if (empty($request->meta_title))
      $options["meta_title"] = $product->title;
    else
      $options["meta_title"] = $request->meta_title;
  
  
    if (empty($request->meta_description))
      $options["meta_description"] = substr(strip_tags($product->description), 0, 150);
    else
      $options["
          meta_description"] = $request->meta_description;
  
  
    if (empty($request->meta_keyword))
      $options["meta_keyword"] = $product->meta_keyword;
    else
      $options["meta_keyword"] = $request->meta_keyword;
  
    $product->options = json_encode($options);
  }
}
