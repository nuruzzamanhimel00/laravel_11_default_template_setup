<template>
    <div class="table-responsive">
        <table class="table table-bordered ic-table-return">
          <thead>
            <tr>
              <th rowspan="2" class="align-middle">SL</th>
              <th rowspan="2" class="align-middle">SKU</th>
              <th rowspan="2" class="align-middle">Product Name</th>
              <th colspan="3" class="text-center">Purchase Receive</th>
              <!-- <th width="10%" class="text-center">Product Stock</th> -->
              <th width="10%" class="text-center">Return</th>
              <th colspan="3" class="text-center">Purchase Return</th>
            </tr>
            <tr>
              <th>Quantity</th>
              <th>Price</th>
              <th>Sub Total</th>
              <th class="text-center">Quantity</th>
              <!-- <th class="text-center">Quantity</th> -->
              <th>Quantity</th>
              <th>Price</th>
              <th>Sub Total</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in purchaseReturnItems" :key="index">
              <td>{{ index + 1 }}</td>
              <td>
                {{ item . product_sku }}
                <input type="hidden" :name="`purchase_return[${index}][product_id]`" v-model="item.product_id">
                <input type="hidden" :name="`purchase_return[${index}][purchase_item_id]`" v-model="item.purchase_item_id">
                <input type="hidden" :name="`purchase_return[${index}][product_variant_id]`" v-model="item.product_variant_id">
                <input type="hidden" :name="`purchase_return[${index}][warehouse_id]`" v-model="item.warehouse_id">
              </td>
              <td>
                {{ item.product_name }}
              </td>
              <td>
                <span>{{item.total_receive_quantity}}</span>
              </td>
              <td class="text-right">{{currency}} {{item.total_receive_price}} </td>
              <td class="text-right">{{currency}} {{item.total_receive_sub_total}} </td>
              <!-- <td class="text-center">1257</td> -->
              <td class="text-center">{{ item.total_return_quantity }}
              </td>
              <td>
                <input type="text" min="1" @change="calculateTotal(index)" v-model="item.return_quantity" :name="`purchase_return[${index}][return_quantity]`"  class="form-control form-control-sm " :readonly=" item.can_return_quantity <= 0">
                <small v-if="item.can_return_quantity <= 0" class="text-danger">No Available Quantity for Return</small>
              </td>
              <td>
                <input type="text" @change="calculateTotal(index)" v-model="item.return_price"  :name="`purchase_return[${index}][return_price]`" class="form-control form-control-sm ic-return-calculate-input" readonly>
              </td>
              <td>
                <input type="number" step="any"  readonly="readonly" v-model="item.return_sub_total" :name="`purchase_return[${index}][return_sub_total]`"  class="form-control form-control-sm sub_total">
              </td>
            </tr>
          </tbody>
          <tfoot>
            <tr>
              <th colspan="5" class="text-right">Total:</th>
              <th class="text-right"> {{ currency }} {{ purchaseItems.reduce((sum, item) => sum + (parseFloat(item.total_receive_sub_total) || 0), 0) }}
              </th>
              <th colspan="3" class="text-right">Total:</th>
              <th class="text-right">
                <input name="total" readonly="readonly" v-model="total" class="form-control form-control-sm total">
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
        name: 'PurchaseReturnList',
        data() {
            return {
                total: '',
                purchaseReturnItems:[...this.purchaseItems]
            }
        },
        methods: {
            calculateTotal(index) {
                let self = this
                if (self.purchaseReturnItems[index].return_quantity > self.purchaseReturnItems[index].can_return_quantity) {
                    self.purchaseReturnItems[index].return_quantity = self.purchaseReturnItems[index].can_return_quantity
                }
                self.purchaseReturnItems.forEach((item) => {

                    item.return_sub_total = item.return_quantity > 0 && item.return_quantity != ''  ? (item
                        .return_quantity * item.return_price) : 0
                })
                self.total = self.purchaseReturnItems.reduce((sum, item) => sum + (parseFloat(item.return_sub_total) || 0),
                    0)
            }
        }
    }
</script>

<style lang="scss" scoped>

</style>
