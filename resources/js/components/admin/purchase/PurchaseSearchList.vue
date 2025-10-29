
<template>
    <div class="row">
        <div class="form-group col-xl-12 col-lg-12 p-2">
            <label>Search Product</label>

            <div class="ic-form-group position-relative">
                <form @submit.prevent="submitBarcode">
                    <input type="text" id="search_product" class="f-input form-control" v-model="search_product"
                    @input="searchProduct"
                    placeholder="Search Product By Name or SKU" >
                </form>
            </div>

            <ul v-if="products.length > 0" class="list-group">
                <li
                  v-for="(product,index) in products"
                  :key="index"
                  class="list-group-item" style="    cursor: pointer;"
                  @click="addPurchaseItem(product)"
                  >
                 {{ product.product_name }}
                </li>
              </ul>

        </div>

        <div class="form-group col-xl-12 col-lg-12 p-2">
            <label for="notes">Products <span class="error">*</span></label>
            <div class="table-responsive">

                <table class="table">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">SKU</th>

                        <th scope="col">Name</th>
                        <th scope="col">Quantity</th>

                        <th scope="col">Price</th>
                        <th scope="col">User Price</th>
                        <th scope="col">Restaurant Price</th>
                        <th scope="col">Note</th>
                        <th scope="col">Sub Total</th>
                        <th scope="col">
                            <button class="btn btn-outline-danger"
                            @click="deletePurchaseItems"
                            type="button" title="Delete"><i class="mdi mdi-trash-can-outline"></i>
                            </button>
                        </th>
                      </tr>
                    </thead>
                    <tbody v-if="purchase_list.length > 0">
                      <tr v-for="(purchase, index) in purchase_list" :key="purchase.id">


                        <td scope="row">
                            {{ index + 1 }}
                            <input type="hidden" :name="`purchase_items[${index}][product_id]`" v-model="purchase.product_id" />
                            <input type="hidden" :name="`purchase_items[${index}][product_variant_id]`" v-model="purchase.product_variant_id" />
                            <input type="hidden" :name="`purchase_items[${index}][data]`" :value="JSON.stringify(purchase)" />
                            <input type="hidden" :name="`purchase_items[${index}][warehouse_id]`" v-model="warehouse_id" />


                        </td>
                        <td>{{purchase.product_sku}}</td>

                        <td>
                            {{purchase.name}}
                        </td>
                        <td>
                            <input
                            type="number"
                            :name="`purchase_items[${index}][quantity]`"
                            @change="calculateSubTotal(index)"
                            class="form-control"
                            v-model="purchase.quantity"
                            min="1"
                        />
                        </td>

                        <td>
                            <input type="number"  min="1" :name="`purchase_items[${index}][price]`"  class="form-control"
                            @change="calculateSubTotal(index)"
                            v-model="purchase.price" required />
                        </td>
                        <td>
                            <input type="number"  min="1" :name="`purchase_items[${index}][sale_price]`"  class="form-control" v-model="purchase.sale_price" required />
                        </td>
                        <td>
                            <input type="number"  min="1" :name="`purchase_items[${index}][restaurant_sale_price]`"  class="form-control" v-model="purchase.restaurant_sale_price" required/>
                        </td>
                        <td>
                            <input type="text" class="form-control" :name="`purchase_items[${index}][notes]`" v-model="purchase.notes"  />
                        </td>
                        <td>
                            <input type="number" step="any" min="1" class="form-control" :name="`purchase_items[${index}][sub_total]`" v-model="purchase.sub_total" readonly />
                        </td>
                        <td>
                            <button class="btn btn-outline-danger"  type="button" title="Delete"
                            @click="purchase_list.splice(index, 1)"
                            ><i class="mdi mdi-trash-can-outline"></i>
                            </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>

            </div>

        </div>
    </div>
</template>

<script>
    export default {
        props: {
            purchaseItems: {
                type: Array, // Accepts String or Number
                default: null,
            },
        },

        name:'PurchaseSearchList',
        data(){
            return {
                search_product: '',
                products:[],
                purchase_list:[],
                isTimeout: null,
                warehouse_id:''
            }
        },

        mounted(){

            this.initializeData();
            this.$nextTick(() => {
                this.warehouse_id = document.getElementById('warehouse_id').value
            })
        },
        methods: {
            calculateSubTotal(index){
                if(this.purchase_list[index].price <=0 ){
                    this.purchase_list[index].price = this.purchase_list[index].old_price;

                }else if(this.purchase_list[index].quantity <= 0){
                    this.purchase_list[index].quantity = 1;

                }
                let total = this.purchase_list[index].quantity * this.purchase_list[index].price;
                this.purchase_list[index].sub_total = total;
            },
            initializeData(){
                if(this.purchaseItems != null){
                    this.purchase_list = this.purchaseItems.map((item)=>{
                        return {
                            ...item,
                            old_price: item.price
                        }
                    });

                }
            },
            deletePurchaseItems(){
                this.purchase_list = [];
            },
            addPurchaseItem(product){
                let find = ''
                let findIndex = ''
                if(product.product_variant_id != ''){
                     find = this.purchase_list.find(item => item.product_id == product.product_id && item.product_variant_id == product.product_variant_id);
                     findIndex = this.purchase_list.findIndex(item => item.product_id == product.product_id && item.product_variant_id == product.product_variant_id);
                }else{
                     find = this.purchase_list.find(item => item.product_id == product.product_id);
                     findIndex = this.purchase_list.findIndex(item => item.product_id == product.product_id);
                }
                this.search_product = '';
                this.products = [];

                if(find){
                    find.quantity += 1
                    this.calculateSubTotal(findIndex)
                    // alert('Product already added.')
                    return;
                }
                this.purchase_list.push({
                    ...product,
                    old_price:product.price
                });
            },
            searchProduct() {
                this.handleSearch()
            },
            submitBarcode() {
                this.handleSearch(); // Call the helper function
            },
            handleSearch(delay = 500) {
                let self = this;

                this.warehouse_id = document.getElementById('warehouse_id').value
                self.products = [];
                clearTimeout(this.isTimeout); // Clear any previous timeout
                // Set a new timeout to execute the search after a delay
                this.isTimeout = setTimeout(() => {
                    axios.get(`/search-product-for-purchase?search=${this.search_product}&warehouse_id=${this.warehouse_id}`)
                    .then(function (response) {
                        // handle success
                        if(response.data.length > 0){
                            if(response.data.length == 1){
                                self.addPurchaseItem(response.data[0]);
                            }else{

                                self.products = response.data;
                            }
                        }
                        console.log(response);
                    })
                    .catch(function (error) {
                        // handle error
                        console.log(error);
                    })
                    .finally(function () {
                        // always executed
                    });
                }, delay); // Adjust the timeout delay (500ms in this case)
            },
        }
    }
</script>


