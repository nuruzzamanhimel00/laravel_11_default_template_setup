
<template>
    <div class="row mt-4">

        <div class="form-group col-sm-8 col-lg-8">
            <label>Attribute Items</label>
            <div class="table-responsive">
              <table class="table table-bordered table-sm">
                <thead>
                  <tr>
                    <th> Item Name</th>
                    <th> Color</th>

                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in attribute_items" :key="index">
                    <td width="40%">
                      <input type="text" :name="`item_data[${index}][value]`"
                      v-model="item.value"
                      maxlength="100" placeholder="Item name" class="form-control">
                    </td>
                    <td width="20%"><input type="color" :name="`item_data[${index}][color]`" v-model="item.color" class="form-control"></td>
                    <td>
                        <img v-if="item.image" :src="item.image_url" alt="" class=" float-left" width="50px">
                        <input type="hidden" :name="`item_data[${index}][old_image]`" v-model="item.image" />
                        <input type="file" :name="`item_data[${index}][image]`"  class="form-control">
                    </td>
                    <td>
                      <button type="button" class="btn btn-sm btn-outline-danger"  @click="confirmDelete(index)">
                        <i class="fa fa-trash"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <button type="button" class="btn btn-info btn-sm" @click="addAttributeItem">
              <i class="fa fa-plus"></i> Add Item </button>
          </div>
    </div>

</template>

<script>
import { initial } from 'lodash';

    export default {
        props: {
            attributeItemsData: {
                type: Array, // Accepts String or Number
                default: null,
            },
        },

        name:'AttributeItems',
        data(){
            return {
                attribute_items:[
                    {
                        value:'',
                        color:'',
                        image:'',
                    }
                ]
            }
        },
        mounted(){
            this.initializeData();
        },
        methods:{
            confirmDelete(index) {
                if (window.confirm("Are you sure you want to delete this item?")) {
                this.attribute_items.splice(index, 1);
                }
            },
            initializeData(){
                if(this.attributeItemsData != null){
                    this.attribute_items = this.attributeItemsData
                }
            },
            addAttributeItem(){
                this.attribute_items.push({
                    value:''
                })
            }
        }
    }
</script>

<style lang="scss" scoped>

</style>
