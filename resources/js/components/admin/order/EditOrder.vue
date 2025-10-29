<template>
    <div class="row">

        <input type="hidden" :value="JSON.stringify(form.billing_info)" name="billing_info"/>
        <input type="hidden" :value="JSON.stringify(form.shipping_info)" name="shipping_info"/>
        <input type="hidden" :value="walk_in_customer" name="walk_in_customer"/>

        <input type="hidden" :value="form.total_paid" name="total_paid"/>
        <input type="hidden" :value="form.tax_vat" name="tax_amount"/>
        <input type="hidden" name="sub_total"  :value="form.sub_total" />
        <input type="hidden" :value="form.discount_amount" name="discount_amount"/>
        <input type="hidden" :value="form.order_for" name="order_for"/>
        <input type="hidden" :value="is_split_sale" name="is_split_sale"/>


        <div class="col-12">
            <div class="row">
                <!-- Product List Column -->
                <div class="col-lg-5 col-xl-6">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6 mb-2 mb-md-0">
                                    <label class="form-label">
                                        Order For


                                    </label>
                                    <div class="row gap-4">
                                        <div
                                            class="d-flex align-items-center custom-control custom-radio custom-control-inline col-md-4">
                                            <input type="radio" id="order_for_customer"
                                                name="order_for" value="Customer" @change="form.order_for_id = ''" v-model="form.order_for"
                                                class="custom-control-input order_for_radio" disabled>
                                            <label
                                                class="block font-medium text-sm text-gray-700 mb-0 mx-2 custom-control-label"
                                                for="order_for_customer">
                                                Customer

                                            </label>
                                        </div>

                                        <div
                                            class="d-flex align-items-center custom-control custom-radio custom-control-inline col-md-4">
                                            <input type="radio" id="order_for_restaurant"
                                                name="order_for" value="Restaurant" @change="form.customer_id = ''" v-model="form.order_for"
                                                class="custom-control-input order_for_radio" disabled>
                                            <label
                                                class="block font-medium text-sm text-gray-700 mb-0 mx-2 custom-control-label"
                                                for="order_for_restaurant">
                                                Restaurant

                                            </label>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-6 mb-2 mb-md-0">
                                    <label for="order_for_id" class="form-label">Select {{ form.order_for }} </label>
                                    <select class="form-select" name="order_for_id" v-model="form.order_for_id" @change="orderForChange"  v-if="form.order_for == 'Customer'" required>
                                        <option value="" selected>Select One</option>
                                        <option v-for="customer in regular_users" :key="customer.id" :value="customer.id">{{ customer.full_name }}</option>

                                      </select>
                                      <select class="form-select" name="order_for_id" v-model="form.order_for_id" @change="orderForChange"  required v-else>
                                        <option value="" selected>Select One</option>
                                        <option v-for="customer in restaurants" :key="customer.id" :value="customer.id">{{ customer.full_name }}</option>
                                      </select>
                                </div>
                                <div class="col-md-12 mb-2 mb-md-0">
                                    <form @submit.prevent="submitBarcode">
                                        <div class="form-group">
                                            <label class="typo__label">Search Product </label>
                                          <input type="text" placeholder="Search Product or Scan Barcode" class="form-control"
                                          v-model="search_product"
                                            @input="searchProduct"
                                          >
                                        </div>
                                      </form>
                                </div>

                            </div>

                            <div class="row order-scroll"
                                ref="scrollContainer"
                                 @scroll="handleScroll"
                            >
                                <!-- Order Items -->
                                <div v-for="(data) in allProducts" :key="data.id" @click="addProduct(data)" class="col-sm-6 col-md-4 col-lg-6 col-xl-4 mb-3" style="cursor: pointer;">
                                    <div class="product-item">
                                        <div class="text-center p-2">
                                            <img :src="data.product.image_url"
                                                alt="Product" class="img-fluid list-image">
                                        </div>
                                        <div class="product-item-body">
                                            <p class="m-0 fw-bold">{{data.product.name}}
                                                <span v-if="data.product.rating != ''">
                                                    <i class="fas fa-star rating"></i>{{data.product.rating.averageRating ?? ''}} ({{
                                                        data.product.rating.totalReviewCount
                                                    }})
                                                </span>

                                            </p>
                                            <p class="card-text p-0 m-0">{{ data.product.product_meta.unit_value ?? '1' }} {{ data.product.product_unit.name ?? '' }}</p>

                                            <p class="card-text p-0 m-0">Price:
                                                {{ currency }}     {{ order_for == 'Customer' ? data.product.sale_price  : data.product.restaurant_sale_price}}  </p>
                                            <p class="card-text p-0 m-0">Stock: {{data.stock_quantity}}</p>
                                            <p class="card-text p-0 m-0">Warehouse: {{data.warehouse.name}}</p>
                                            <span v-if="data.promotion_items.length > 0" class="badge rounded-pill bg-success"
                                                data-bs-toggle="tooltip" data-bs-placement="top" :title="`Title: ${data.promotion_items[0].promotion.title}`"
                                                >Promoted</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 text-center">
                                    <div v-if="loading" class="loading">
                                        Loading...
                                      </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Invoice Form Column -->
                <div class="col-lg-7 col-xl-6">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="form-label">{{form.order_for }}</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="walkinCustomer" v-model="walk_in_customer"  @change="walkInCustomer">
                                                <label class="form-check-label" for="walkinCustomer" >
                                                    Walk-in {{ form.order_for  }}
                                                </label>
                                            </div>
                                        </div>

                                    </div>
                                    <div v-if="walk_in_customer">
                                        <div class="mb-3 w-100">
                                            <div class="form-group mt-2">
                                                <label for="name">{{ form.order_for  }} Name <span class="error">*</span></label>
                                                <input type="text" id="name" name="customer[name]" class="form-control" required>
                                            </div>
                                            <div class="form-group mt-2">
                                                <label for="email">{{ form.order_for  }} Email <span class="error">*</span></label>
                                                <input type="text" id="email" name="customer[email]" class="form-control" required>
                                            </div>
                                            <div class="form-group mt-2">
                                                <label for="phone">{{ form.order_for  }} Phone <span class="error">*</span></label>
                                                <input type="text" id="phone" name="customer[phone]" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12  mt-3">
                                    <div class="p-0">
                                      <label class="text-muted w-100 mb-0">Billing info <span class="error">*</span>

                                        <button type="button" class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#billingInfoModal">
                                            <i class="fa fa-edit"></i>
                                          </button>
                                      </label>
                                    </div>
                                    <div class=" mt-1 float-start" v-if="form.billing_info.full_name != '' && form.billing_info.email">
                                      <p class="m-0">{{form.billing_info.full_name}}</p>
                                      <p class="m-0">{{form.billing_info.email}}</p>
                                      <p class="m-0">{{form.billing_info.phone}}</p>
                                      <p class="m-0">{{form.billing_info.address}}</p>
                                    </div>
                                    <div v-else>, , , , , , , ,</div>
                                  </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12  mt-3">
                                    <div class="">
                                      <label class="text-muted mb-0 w-100">Shipping info <span class="error">*</span>

                                        <button type="button" class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#ShippingInfoModal">
                                            <i class="fa fa-edit"></i>
                                          </button>
                                      </label>
                                      <div class="form-check">
                                        <input type="checkbox" id="shippingSameBilling" class="form-check-input" @change="sameAsBillingHandler">
                                        <label for="shippingSameBilling" class="form-check-label" >Same as billing</label>
                                      </div>
                                    </div>
                                    <div class="mt-1  float-start" v-if="form.shipping_info.full_name != '' && form.shipping_info.email">
                                        <p class="m-0">{{form.shipping_info.full_name}}</p>
                                        <p class="m-0">{{form.shipping_info.email}}</p>
                                        <p class="m-0">{{form.shipping_info.phone}}</p>
                                        <p class="m-0">{{form.shipping_info.address}}</p>
                                      </div>
                                      <div v-else>, , , , , , , ,</div>
                                  </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="invoiceDate">Date</label>
                                <input type="date" class="form-control" id="invoiceDate"  name="date" v-model="form.date">
                            </div>


                            <div class="table-responsive mb-3">
                                <table class="table table-sm table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Item</th>
                                            <th class="text-center">Price ({{currency}})</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-center">Dis</th>
                                            <th class="text-center">Dis Type</th>
                                            <th class="text-center">Sub Total ({{currency}})</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, index) in saleItems" :key="item.id">

                                            <td width="20%" >{{item.product_name}} <br>

                                                <input type="hidden" :name="`sale_items[${index}][product_id]`" :value="item.product_id" />
                                                <input type="hidden" :name="`sale_items[${index}][variant_id]`" value="" />
                                                <input type="hidden" :name="`sale_items[${index}][product_name]`" :value="item.product_name" />
                                                <input type="hidden" :name="`sale_items[${index}][product_sku]`" :value="item.product_sku" />
                                                <input type="hidden" :name="`sale_items[${index}][product_barcode]`" :value="item.product_barcode" />
                                                <input type="hidden" :name="`sale_items[${index}][sub_total]`" :value="item.sub_total" />

                                                <input type="hidden" :name="`sale_items[${index}][warehouse_id]`" :value="item.warehouse_id" />
                                                <input type="hidden" :name="`sale_items[${index}][warehouse_stock_id]`" :value="item.warehouse_stock_id" />
                                                <input type="hidden" :name="`sale_items[${index}][data]`" :value="JSON.stringify(item)" />

                                            </td>
                                            <td width="15%">
                                                <input type="number" min="1" step="any" :name="`sale_items[${index}][price]`" v-model="item.regular_price" @change="calculateEachSubTotal(item)" class="form-control text-center">
                                            </td>
                                            <td width="15%">
                                                <input type="number" min="1"  :name="`sale_items[${index}][quantity]`"  v-model="item.quantity" @change="calculateEachSubTotal(item)" class="form-control text-center">
                                            </td>
                                            <td width="15%">
                                                <input type="number" min="0" step="any"   :name="`sale_items[${index}][discount]`" v-model="item.discount" @change="calculateEachSubTotal(item)" class="form-control text-center">
                                            </td>
                                            <td width="15%">
                                                <select class="form-select"  :name="`sale_items[${index}][discount_type]`"  v-model="item.discount_type" @change="calculateEachSubTotal(item)">
                                                    <option value="percent">%</option>
                                                    <option value="fixed">Fixed</option>
                                                </select>
                                            </td>
                                            <td width="15%" class="text-center">

                                                {{item.sub_total}}

                                            </td>
                                            <td width="5%" class="text-center">
                                                <button type="button" class="btn btn-sm btn-outline-danger" @click="saleItemDltHandler(item)">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>

                                        <tr>
                                            <td colspan="5"><b>Sub Total</b></td>
                                            <td class="text-center"><b>{{form.sub_total}}</b></td>
                                            <td>      </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><b>Discount</b></td>
                                            <td>
                                                <input type="number" class="form-control text-center" name="global_discount" @change="globalDiscountChange" v-model="form.global_discount" >


                                            </td>
                                            <td colspan="2">
                                                <select class="form-select text-center" @change="globalDiscountChange" name="global_discount_type"  v-model="form.global_discount_type">
                                                    <option value="percent">%</option>
                                                    <option value="fixed">Fixed</option>
                                                </select>
                                            </td>
                                            <td class="text-center"><b>{{form.discount_amount}}</b></td>
                                            <td>    <input type="hidden" :value="form.discount_amount" name="discount_amount"/></td>
                                        </tr>
                                        <tr>
                                            <td colspan="5"><b>Total Discount</b></td>
                                            <td class="text-center"><b>{{form.discount_amount}}</b></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="5"><b>Tax/Vat</b></td>
                                            <td class="text-center"><b>{{form.tax_vat}}</b></td>
                                            <td></td>
                                        </tr>

                                        <tr>
                                            <td colspan="5"><b>Total</b></td>
                                            <td class="text-center"><b>{{form.total}}</b></td>
                                            <td>

                                                <input type="hidden" :value="form.total" name="total"/></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>



                            <div class="col-sm-12 p-0">
                                <label for="" class="w-100">Payment</label>
                                <div class="d-flex gap-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" value="cash" id="cash" name="payment_type" v-model="form.payment_type">
                                        <label class="form-check-label" for="cash">
                                            <img src="/images/default/cash.png" alt="Cash">
                                        </label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" value="bank" id="bank" name="payment_type" v-model="form.payment_type">
                                        <label class="form-check-label" for="bank">
                                            <img src="/images/default/bank.png" alt="Bank">
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="totalPaid" class="form-label">Total Paid {{is_split_sale ? '(Can Split Sale)' : '(Can Not Split Sale)'}} </label>
                                    <input type="number" min="0" step="any" id="totalPaid" class="form-control" v-model="form.total_paid"
                                    name="total_paid"
                                    @change="totalPaidChangeHandler"
                                    :readonly="!is_split_sale">
                                </div>

                                <div class="mb-3" v-if="form.payment_type == 'bank'">
                                    <label for="accountNumber" class="form-label">Account Number <span class="error">*</span></label>
                                    <input type="text" id="accountNumber" class="form-control" v-model="form.payment_info.account_no" name="payment_info[account_no]"
                                     required>
                                </div>

                                <div class="mb-3" v-if="form.payment_type == 'bank'">
                                    <label for="transactionNo" class="form-label">Transaction No <span class="error">*</span></label>
                                    <input type="text" id="transactionNo" class="form-control" v-model="form.payment_info.transaction_no" name="payment_info[transaction_no]" required>
                                </div>

                                <div class="mb-3" v-if="form.payment_type == 'bank'">
                                    <label for="transactionDate" class="form-label">Transaction Date <span class="error">*</span></label>
                                    <input type="date" id="transactionDate" class="form-control" v-model="form.payment_info.date" name="payment_info[date]" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Note</label>
                                <textarea cols="30" rows="10" class="form-control" v-model="form.payment_info.notes"  name="payment_info[notes]"></textarea>
                              </div>
                              <div class="col-sm-12 p-0 mt-3">
                                <div class="text-center"></div>
                              </div>
                              <div class="col-sm-12 p-0 mt-3">
                                <div class="d-flex justify-content-between align-items-center">
                                  <!-- Confirm Button -->
                                  <button type="submit" class="btn btn-primary btn-lg flex-grow-1 me-2">
                                    Confirm
                                  </button>

                                  <!-- Reset Button -->
                                  <a href="/orders" class="btn btn-outline-secondary text-dark">
                                    <i class="fa fa-times"></i> Back
                                  </a>
                                </div>
                              </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
         <!-- Billing Info Model-->
         <div class="modal fade" id="billingInfoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" >
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Billing info</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                          <label for="name" class="form-label">Name</label>
                          <input type="text" id="name" class="form-control" v-model="form.billing_info.full_name">
                        </div>
                        <div class="col-md-4">
                          <label for="email" class="form-label">Email</label>
                          <input type="email" id="email" class="form-control" v-model="form.billing_info.email">
                        </div>
                        <div class="col-md-4">
                          <label for="phone" class="form-label">Phone</label>
                          <input type="text" id="phone" class="form-control" v-model="form.billing_info.phone">
                        </div>
                        <div class="col-md-4">
                          <label for="address1" class="form-label">Address</label>
                          <input type="text" id="address1" class="form-control" v-model="form.billing_info.address">
                        </div>

                        <div class="col-md-4">
                          <label for="city" class="form-label">City</label>
                          <input type="text" id="city" class="form-control" v-model="form.billing_info.city">
                        </div>

                        <div class="col-md-4">
                          <label for="zip" class="form-label">Zip Code</label>
                          <input type="text" id="zip" class="form-control" v-model="form.billing_info.zipcode">
                        </div>
                        <div class="col-md-4">
                          <label for="country" class="form-label">Country</label>
                          <input type="text" id="country" class="form-control" v-model="form.billing_info.country">
                        </div>
                      </div>

                </div>
                <div class="modal-footer">
                  <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
                  <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Save changes</button>
                </div>
              </div>
            </div>
          </div>
          <!-- Billing Info Model end-->
          <!-- Shipping Info Model-->
          <div class="modal fade" id="ShippingInfoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" >
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Shipping info</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                          <label for="name" class="form-label">Name</label>
                          <input type="text" id="name" class="form-control" v-model="form.shipping_info.full_name">
                        </div>
                        <div class="col-md-4">
                          <label for="email" class="form-label">Email</label>
                          <input type="email" id="email" class="form-control" v-model="form.shipping_info.email">
                        </div>
                        <div class="col-md-4">
                          <label for="phone" class="form-label">Phone</label>
                          <input type="text" id="phone" class="form-control" v-model="form.shipping_info.phone">
                        </div>
                        <div class="col-md-4">
                          <label for="address1" class="form-label">Address</label>
                          <input type="text" id="address1" class="form-control" v-model="form.shipping_info.address">
                        </div>

                        <div class="col-md-4">
                          <label for="city" class="form-label">City</label>
                          <input type="text" id="city" class="form-control" v-model="form.shipping_info.city">
                        </div>

                        <div class="col-md-4">
                          <label for="zip" class="form-label">Zip Code</label>
                          <input type="text" id="zip" class="form-control" v-model="form.shipping_info.zipcode">
                        </div>
                        <div class="col-md-4">
                          <label for="country" class="form-label">Country</label>
                          <input type="text" id="country" class="form-control" v-model="form.shipping_info.country">
                        </div>
                      </div>

                </div>
                <div class="modal-footer">
                  <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
                  <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Save changes</button>
                </div>
              </div>
            </div>
          </div>
    </div>
</template>

<script>


    export default {
        name:'EditOrder',
        props: {

            regular_users: {
                type: Array, // Accepts String or Number
                default: null,
            },
            restaurants: {
                type: Array, // Accepts String or Number
                default: null,
            },
            currency: {
                type: String, // Accepts String or Number
                default: null,
            },
            order_for: {
                type: String, // Accepts String or Number
                default: null,
            },
            sale: {
                type: Object, // Accepts String or Number
                default: null,
            },
        },

        data(){
            return {
                is_split_sale: false,
                loading: false,
                hasMore: true,
                walk_in_customer:false,
                search_product:'',
                page:1,
                allProducts:[],
                customer: {},
                saleItems:[],
                form:{
                    order_for:this.order_for,
                    order_for_id:'',
                    sub_total:0,
                    total:0,
                    discount_amount:0,
                    tax_vat:0,
                    global_discount:0,
                    global_discount_type:null,
                    billing_info:{
                        full_name:'',
                        email:'',
                        phone:'',
                        address:'',
                        city:'',
                        state:'',
                        zipcode:'',
                        country:'',
                    },
                    shipping_info:{
                        full_name:'',
                        email:'',
                        phone:'',
                        address:'',
                        city:'',
                        state:'',
                        zipcode:'',
                        country:'',
                    },
                    date:'',
                    total_paid:0,
                    last_paid:'',
                    payment_type:'cash',
                    // notes:'',
                    payment_info:{
                        account_no:'',
                        transaction_no :'',
                        transaction_date :'',
                    }
                }
            }
        },
         created(){
             this.initialDataLoad()
        },
        mounted(){
            this.load();
        },
        methods:{
            initialDataLoad() {
                const self = this;
                const sale = self.sale;

                // Set customer and form data
                self.customer = sale.customer;
                self.is_split_sale = sale.is_split_sale;

                Object.assign(self.form, {
                    order_for: sale.order_for,
                    order_for_id: sale.order_for_id,
                    sub_total: sale.sub_total,
                    total: sale.total,
                    discount_amount: sale.discount_amount,
                    tax_vat: sale.tax_amount,
                    global_discount: sale.global_discount,
                    global_discount_type: sale.global_discount_type,
                    billing_info: sale.billing_info,
                    shipping_info: sale.shipping_info,
                    date: sale.date,
                    total_paid: sale.total_paid,
                    payment_type: sale.payment_type,
                    payment_info: sale.order_payment?.account_info ?? '',
                });

                // Load sale items
                self.saleItems = sale.order_items.map(item => ({
                    id: item.id,
                    warehouse_stock_id: item.warehouse_stock_id,
                    product_id: item.product_id,
                    product_variant_id: item.product_variant_id,
                    warehouse_id: item.warehouse_id,
                    product_name: item.product_name,
                    product_sku: item.product_sku,
                    product_barcode: item.product_barcode,
                    sale_price: item.price,
                    regular_price: item.price,
                    quantity: item.quantity,
                    stock_quantity: item.warehouse_stock.stock_quantity,
                    discount_type: item.discount_type,
                    discount: item.discount,
                    sub_total: item.sub_total,
                    taxes: {
                        has_tax: item.product?.taxes?.has_tax ?? false,
                        tax_amount: item.product?.taxes?.tax_amount ?? 0,
                    },
                    is_split_sale:item.product.is_split_sale,
                }));

                // Optionally recalculate totals
                // self.totalCalculate();
                self.splitSaleCalculate();
            },

            saleItemDltHandler(item) {
                let self = this;

                // Add a confirmation prompt
                if (confirm(`Are you sure you want to delete "${item.product_name || 'this item'}"?`)) {
                    self.saleItems = self.saleItems.filter((i) => {
                        return !(i.product_id === item.product_id && i.warehouse_id === item.warehouse_id);
                    });

                    self.totalCalculate();

                    if (self.saleItems.length === 0) {
                        self.resetSaleData();
                    }
                    self.splitSaleCalculate();
                }
            },

            totalPaidChangeHandler(){
                let self = this
                if(self.form.total_paid > self.form.total){
                    alert('Paid Amount should not be greater than total amount');
                    self.form.total_paid = self.form.total
                }
            },
            addProduct(stock) {
                let self = this;
                let find = self.saleItems.find((item) => item.product_id === stock.product_id && item.warehouse_id === stock.warehouse_id);
                let stockQuantity
                if (find) {
                    console.log('add product',find)
                    find.quantity += 1;
                    self.totalCalculate()
                    self.splitSaleCalculate();
                    return;
                }

                if (parseInt(stock.stock_quantity) <= 0) {
                    alert('Low Stock!!');
                    return;
                }
                // console.log('find',stock)
                // self.saleItems.push({ ...stock,quantity: 1,discount_type:null,discount:null,sub_total:0, sale_price:self.form.order_for == 'Customer' ? stock.sale_price : stock.restaurant_sale_price });

                self.saleItems.push({
                    id: stock.id,
                    warehouse_stock_id: stock.id,
                    warehouse_id: stock.warehouse_id,
                    product_id: stock.product_id,
                    product_variant_id:null,
                    stock_quantity: stock.stock_quantity,

                    restaurant_sale_price: stock.product.restaurant_sale_price,
                    sale_price: stock.product.sale_price,
                    regular_price: self.order_for == 'Customer' ? stock.product.sale_price : stock.product.restaurant_sale_price,

                    product_name : stock.product.name,
                    product_sku : stock.product.sku,
                    product_barcode : stock.product.barcode,
                    sub_total : 0,

                    quantity : 1,
                    discount_type: stock.promotion_items.length > 0 ? stock.promotion_items[0].promotion.offer_type : null,
                    discount:stock.promotion_items.length > 0 ? stock.promotion_items[0].promotion.offer_value : null,
                    taxes:{
                        has_tax:stock.product.taxes.has_tax,
                        tax_amount:stock.product.taxes.tax_amount,
                    },
                    is_split_sale:stock.product.is_split_sale,


                });
                self.totalCalculate()
                self.splitSaleCalculate();
            },
            splitSaleCalculate(){
                let self = this
                let findIsSplit = self.saleItems.find((item) => item.is_split_sale == 1);
                if(findIsSplit){
                    self.is_split_sale = true
                }else{
                    self.is_split_sale = false
                }
            },
            totalCalculate(){
                this.saleItems.forEach((item)=>{
                    this.calculateEachSubTotal(item);
                })
            },
            async calculateEachSubTotal(item){
                let self = this
                // let payout_total = 0;
                if(item.quantity > item.stock_quantity){
                    item.quantity = item.stock_quantity
                }
                // let price =  self.form.order_for == 'Customer' ? item.sale_price : item.restaurant_sale_price
                let price =  item.regular_price
                let sub_total = parseFloat(price) * parseInt(item.quantity);

                if(item.discount_type != null && item.discount_type == 'percent'){
                    sub_total = sub_total - (sub_total * (item.discount / 100))
                }else if(item.discount_type != null && item.discount_type == 'fixed'){
                    sub_total = sub_total - item.discount
                }
                sub_total = parseFloat(sub_total).toFixed(2)


                item.sub_total = sub_total;
                // item.payout_total = await self.calculatePayoutTotal(sub_total, self.platformFees);



                await this.globalDiscountChange();

            },
            async globalDiscountChange(){
                let self= this;
                self.form.sub_total = self.saleItems.reduce((sum, item) => sum + (parseFloat(item.sub_total) || 0), 0)
                self.form.sub_total = parseFloat(self.form.sub_total).toFixed(2)
                //vat tax calculate for each item
                let tax_vat = 0;
                self.saleItems.forEach((item)=>{
                    if(item.taxes.has_tax == 1 && item.taxes.tax_amount != null){
                        tax_vat += parseFloat(item.sub_total) * (parseFloat(item.taxes.tax_amount) / 100)
                    }

                })
                self.form.tax_vat =tax_vat.toFixed(2)


                if(this.form.global_discount_type != null && this.form.global_discount_type == 'percent'){
                    let discount_amount = parseFloat(this.form.sub_total) * (this.form.global_discount / 100);
                    this.form.discount_amount = discount_amount.toFixed(2)
                }else if(this.form.global_discount_type != null && this.form.global_discount_type == 'fixed'){
                    this.form.discount_amount = this.form.global_discount != null ? parseFloat(this.form.global_discount) : 0
                }
                let total = (parseFloat(this.form.sub_total) - this.form.discount_amount ?? 0) + parseFloat(this.form.tax_vat)
                this.form.total = parseFloat(total).toFixed(2) //total
                this.form.total_paid = parseFloat(total).toFixed(2) //total
            },
            sameAsBillingHandler(){
                this.form.shipping_info = this.form.billing_info
            },
            orderForChange(){
                let self = this
                self.customer = {}
                if(self.form.order_for == 'Customer'){
                    let selectedUser = self.regular_users.find(user => user.id == self.form.order_for_id)
                    self.customer = selectedUser
                    self.setBillingInfo(selectedUser)
                }else{
                    let selectedUser = self.restaurants.find(user => user.id == self.form.order_for_id)
                    self.customer = selectedUser
                    self.setBillingInfo(selectedUser)
                }

            },
            setBillingInfo(user){
                this.form.billing_info  = {
                        ...this.form.billing_info ,
                        full_name: user.full_name,
                        email: user.email,
                        phone: user.phone,
                        address: this.form.order_for == 'Restaurant' ? user.restaurant.address : '',
                    }
            },
            walkInCustomer(){
                this.customer = {}
                // this.resetSaleData()
            },
            resetSaleData(){
                this.form = {
                    ...this.form ,
                    tax_vat:0,
                    sub_total:0,
                    total:0,
                    discount_amount:0,
                    global_discount:0,
                    global_discount_type:null,
                    total_paid:0,
                    last_paid:'',
                    payment_type:'cash',
                }
                Object.keys(this.form.shipping_info).forEach(key => {
                        this.form.shipping_info[key] = ''; // Reset all fields to empty
                    });
                Object.keys(this.form.billing_info).forEach(key => {
                        this.form.billing_info[key] = ''; // Reset all fields to empty
                    });
                Object.keys(this.form.payment_info).forEach(key => {
                        this.form.payment_info[key] = ''; // Reset all fields to empty
                    });
            },
            searchProduct() {
                this.page = 1
                this.handleSearch(); // Call the helper function
            },
            submitBarcode() {
                this.page = 1
                this.handleSearch(); // Call the helper function
            },
             // Helper function to handle the common logic
            handleSearch(delay = 500) {
                let self = this;
                self.allProducts = [];
                clearTimeout(this.isTimeout); // Clear any previous timeout
                // Set a new timeout to execute the search after a delay
                self.isTimeout = setTimeout(() => {
                    this.hasMore = true;
                    self.load();

                }, delay); // Use the provided delay or default to 500ms
            },
            handleScroll() {
                const container = this.$refs.scrollContainer;

                // Check if user scrolled to the bottom
                if (
                    container.scrollTop + container.clientHeight >=
                    container.scrollHeight - 10
                ) {
                    this.load();
                }
            },
            async load() {
                let self = this
                if (this.loading || !this.hasMore) return;

                this.loading = true;
                try {
                    // Simulate API request
                    const response = await fetch(`/search-order-product?search=${this.search_product}&page=${this.page}&order_for=${this.form.order_for}`);
                    const json = await response.json();
                    console.log('json',json.data)

                    if (json.data.length == 0) {
                        this.hasMore = false;
                        // $state.complete();
                    }
                    else if (json.data.length == 1) {
                        this.hasMore = false;
                        self.addProduct(json.data[0]);
                        self.allProducts.push(json.data[0]);
                        // $state.complete();
                    }
                    else {
                        json.data.forEach((product) => {
                            let findProduct = self.allProducts.find(item => item.id == product.id);
                            if (!findProduct) {
                                self.allProducts.push(product);
                            }
                        });
                        this.page++;
                    }
                } catch (error) {
                    console.error("Error loading items:", error);
                } finally {
                    this.loading = false;
                }
            },

        }
    }
</script>

<style scoped>

</style>
