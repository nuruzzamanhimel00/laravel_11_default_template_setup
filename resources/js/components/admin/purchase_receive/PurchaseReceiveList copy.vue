<template>
    <div class="table-responsive">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th rowspan="2" class="align-middle">SL</th>
              <th rowspan="2" class="align-middle">SKU</th>
              <th rowspan="2" class="align-middle">Product Name</th>
              <th colspan="3" class="text-center">Purchase Order</th>
              <th colspan="3" class="text-center">Purchase Order Received </th>
              <th colspan="3" class="text-center">Purchase Receive</th>
            </tr>
            <tr>
              <th>Quantity</th>
              <th>Price</th>
              <th>Sub Total</th>
              <th>Quantity</th>
              <th>Price</th>
              <th>Sub Total</th>
              <th>Quantity</th>
              <th>Price</th>
              <th>Sub Total</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in purchaseItems" :key="index">
              <td>{{ index + 1 }}</td>
              <td>
                {{ item . product_sku }}
                <input type="hidden" :name="`purchase_receive[${index}][product_id]`" v-model="item.product_id">
                <input type="hidden" :name="`purchase_receive[${index}][purchase_item_id]`" v-model="item.purchase_item_id">
                <input type="hidden" :name="`purchase_receive[${index}][product_variant_id]`" v-model="item.product_variant_id">
                <input type="hidden" :name="`purchase_receive[${index}][warehouse_id]`" v-model="item.warehouse_id">
              </td>
              <td>
                {{ item . product_name }}
              </td>
              <td>
                <span>{{ item . purchase_quantity }}</span>
              </td>
              <td class="text-right">{{currency}} {{ item . purchase_price }} </td>
              <td class="text-right">{{currency}} {{ item . purchase_sub_total }} </td>
              <td>{{ item . total_receive_quantity }}</td>
              <td class="text-right"> {{currency}} {{ item . total_receive_price }} </td>
              <td class="text-right">{{currency}} {{ item . total_receive_sub_total }} </td>
              <td>

                <input type="number"  @change="calculateTotal(index)" v-model="item.receive_quantity" :name="`purchase_receive[${index}][receive_quantity]`" class="form-control form-control-sm ic-calculate-input" :readonly=" item.can_purchase_quantity <= 0" >
              </td>
              <td>
                <input type="number" step="any"  min="1"  @change="calculateTotal(index)" v-model="item.receive_price"  :name="`purchase_receive[${index}][receive_price]`" class="form-control form-control-sm ic-calculate-input">
              </td>
              <td>
                <input type="number" step="any" readonly="readonly" v-model="item.receive_sub_total"  :name="`purchase_receive[${index}][receive_sub_total]`" class="form-control form-control-sm sub_total">
              </td>
            </tr>
          </tbody>
          <tfoot>
            <tr>
              <th colspan="5" class="text-right">Total:</th>
              <th class="text-right"> {{ currency }} {{ purchaseItems.reduce((sum, item) => sum + (parseFloat(item.purchase_sub_total) || 0), 0).toFixed(2) }}
            </th>
              <th colspan="2" class="text-right">Total:</th>
              <th class="text-right">{{ currency }}   {{ purchaseItems.reduce((sum, item) => sum + (parseFloat(item.total_receive_sub_total) || 0), 0).toFixed(2) }} </th>
              <th colspan="2" class="text-right">Total:</th>
              <th class="text-right">
                <input name="total" readonly="readonly" class="form-control total" v-model="total">
              </th>
            </tr>
          </tfoot>
        </table>
      </div>
</template>

<script>
    export default {
        props: {
            purchaseItems: {
                type: Array, // Accepts String or Number
                default: null,
            },
            currency: {
                type: String, // Accepts String or Number
                default: null,
            },
        },
        name: 'PurchaseReceiveList',
        data(){
            return {
                total:''
            }
        },
        methods:{
            calculateTotal(index){
                let self = this
                if(self.purchaseItems[index].receive_quantity > self.purchaseItems[index].can_purchase_quantity){
                    self.purchaseItems[index].receive_quantity  = self.purchaseItems[index].can_purchase_quantity
                    }
                self.purchaseItems.forEach((item)=>{

                    item.receive_sub_total = item.receive_price > 0 && item.receive_quantity > 0 ? (item.receive_quantity * item.receive_price) : 0
                })
                self.total = self.purchaseItems.reduce((sum, item) => sum + (parseFloat(item.receive_sub_total) || 0), 0)
            }
        }
    }
</script>

<style lang="scss" scoped>

</style>
