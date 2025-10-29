<template>
    <div class="table-responsive">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th rowspan="2" class="align-middle">SL</th>
            <!-- <th rowspan="2" class="align-middle">SKU</th> -->
            <th rowspan="2" class="align-middle">Product Name</th>
            <th colspan="5" class="text-center">Purchase Order</th>
            <th colspan="5" class="text-center">Purchase Order Received</th>
            <th colspan="5" class="text-center">Purchase Receive</th>
          </tr>
          <tr>
            <th rowspan="2">Quantity</th>
            <th rowspan="2">Price</th>
            <th rowspan="2">Sub Total</th>
            <th rowspan="2">Quantity</th>
            <th rowspan="2">Price</th>
            <th rowspan="2">Sub Total</th>
            <th rowspan="2">Quantity</th>
            <th rowspan="2">Price</th>
            <th rowspan="2">User Price</th>
            <th rowspan="2">Restaurant Price</th>
            <th rowspan="2">Sub Total</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in purchaseItems" :key="index">
            <td>{{ index + 1 }}
                <input type="hidden" :name="`purchase_receive[${index}][product_id]`" v-model="item.product_id" />
              <input type="hidden" :name="`purchase_receive[${index}][purchase_item_id]`" v-model="item.purchase_item_id" />
              <input type="hidden" :name="`purchase_receive[${index}][product_variant_id]`" v-model="item.product_variant_id" />
              <input type="hidden" :name="`purchase_receive[${index}][warehouse_id]`" v-model="item.warehouse_id" />
            </td>
            <!-- <td>
              {{ item.product_sku }}

            </td> -->
            <td>{{ item.product_name }}</td>
            <td>{{ item.purchase_quantity }}</td>
            <td class="text-right">{{ currency }} {{ item.purchase_price }}</td>
            <td class="text-right">{{ currency }} {{ item.purchase_sub_total }}</td>
            <td>{{ item.total_receive_quantity }}</td>
            <td class="text-right">{{ currency }} {{ item.total_receive_price }}</td>
            <td class="text-right">{{ currency }} {{ item.total_receive_sub_total }}</td>
            <td>
              <input
                type="number"
                min="0"
                @change="calculateTotal(index)"
                v-model.number="item.receive_quantity"
                :name="`purchase_receive[${index}][receive_quantity]`"
                class="form-control form-control-sm ic-calculate-input"
                :readonly="item.can_purchase_quantity <= 0"
              />
            </td>
            <td>
              <input
                type="number"
                min="0"
                @change="calculateTotal(index)"
                v-model.number="item.receive_price"
                :name="`purchase_receive[${index}][receive_price]`"
                class="form-control form-control-sm ic-calculate-input"
                required
              />
            </td>
            <td>
              <input
                type="number"
                min="0"

                v-model.number="item.receive_sale_price"
                :name="`purchase_receive[${index}][receive_sale_price]`"
                class="form-control form-control-sm ic-calculate-input"
                required
              />
            </td>
            <td>
              <input
                type="number"
                min="0"

                v-model.number="item.receive_restaurant_sale_price"
                :name="`purchase_receive[${index}][receive_restaurant_sale_price]`"
                class="form-control form-control-sm ic-calculate-input" required
              />
            </td>
            <td>
              <input
                type="number"
                step="any"
                readonly
                v-model="item.receive_sub_total"
                :name="`purchase_receive[${index}][receive_sub_total]`"
                class="form-control form-control-sm sub_total"
              />
            </td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <th colspan="4" class="text-right">Total:</th>
            <th class="text-right">
              {{ currency }} {{ totalPurchaseSubTotal.toFixed(2) }}
            </th>
            <th colspan="2" class="text-right">Total:</th>
            <th class="text-right">
              {{ currency }} {{ totalReceiveSubTotal.toFixed(2) }}
            </th>
            <th colspan="4" class="text-right">Total:</th>
            <th class="text-right">
              <input name="total" readonly class="form-control total" v-model="total" />
            </th>
          </tr>
        </tfoot>
      </table>
    </div>
  </template>

  <script>
  export default {
    name: "PurchaseReceiveList",
    props: {
      purchaseItems: {
        type: Array,
        default: () => [],
      },
      currency: {
        type: String,
        default: "",
      },
    },
    data() {
      return {
        total: 0,
      };
    },
    computed: {
      totalPurchaseSubTotal() {
        return this.purchaseItems.reduce(
          (sum, item) => sum + (parseFloat(item.purchase_sub_total) || 0),
          0
        );
      },
      totalReceiveSubTotal() {
        return this.purchaseItems.reduce(
          (sum, item) => sum + (parseFloat(item.total_receive_sub_total) || 0),
          0
        );
      },
    },
    methods: {
      calculateTotal(index) {
        const item = this.purchaseItems[index];
        if (item.receive_quantity > item.can_purchase_quantity) {
          item.receive_quantity = item.can_purchase_quantity;
        }

        item.receive_sub_total =
          item.receive_price > 0 && item.receive_quantity > 0
            ? parseFloat((item.receive_price * item.receive_quantity).toFixed(2))
            : 0;

        this.total = this.purchaseItems.reduce(
          (sum, itm) => sum + (parseFloat(itm.receive_sub_total) || 0),
          0
        ).toFixed(2);
      },
    },
  };
  </script>

  <style lang="scss" scoped>
  /* Add your styles here */
  .table-responsive table{
    min-width: 1300px !important;
  }
  </style>
