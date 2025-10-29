<template>
    <div>
        <div class="row">

            <input type="hidden" name="rating" :value="form.rating">
            <input type="hidden" name="user_id" :value="review.user_id">
            <input type="hidden" name="product_id" :value="review.product_id">

            <div class="form-group col-xl-12 col-lg-12 p-2">
                <label>Rating <span class="error">*</span></label>
                <star-rating v-model:rating="form.rating" :increment="0.5" />

            </div>
            <div class="form-group col-xl-12 col-lg-12 p-2">
                <label for="message">Message<span class="error">*</span></label>
                <textarea v-model="form.message" name="message" class="form-control" id="message" rows="3" required></textarea>
            </div>
            <div class="row mb-4">
                <div class="col-md-12 text-end">
                    <a href="#" @click="addImage" class="btn btn-success">Add Images</a>
                </div>

            </div>
            <div class="row" v-for="(image,index) in form.images" :key="index">
                <div class="col-md-6">
                    <input type="file" :name="`images[${index}][image]`" @change="convertToBase64($event,index)" accept="image/*" />
                    <input type="hidden" :name="`images[${index}][old_image]`" :value="image.image" />
                </div>
                <div class="col-md-6 d-flex flex-row justify-content-end align-items-start gap-1">
                    <img :src="image.image_url" width="100" height="100" alt="" class="img-fluid">
                    <a href="#" @click="form.images.splice(index,1)" class="btn btn-danger"><i class="mdi mdi-trash-can-outline"></i></a>
                </div>
                <br>
                <hr/>
            </div>



        </div>
    </div>
</template>

<script>
    import StarRating from 'vue-star-rating'
    import { v4 as uuidv4 } from 'uuid';
    export default {
        name:'EditReview',
        props: ['review'],
        components: {
            StarRating
        },
        data(){
            return{
                form:{
                    rating:0,
                    message:'',
                    images:[]
                }
            }
        },
        created(){
            let self = this
            self.initalize()
        },
        methods:{
            convertToBase64(event,index){
                let self = this
                let file = event.target.files[0];
                let reader = new FileReader();
                reader.onload = (event) => {
                    self.form.images[index].image_url = event.target.result
                    self.form.images[index].image = file
                };
                reader.readAsDataURL(file);
            },
            addImage(){
                let self = this
                self.form.images.push({
                    id:uuidv4(),
                    image:'',
                    image_url:'/images/default/default.png',
                })
            },
            initalize(){
                let self = this
                if(self.review.images && self.review.images.length > 0){
                    self.form.images = self.review.images;
                }
                self.form.rating = self.review.rating
                self.form.message = self.review.message
            }
        }
    }
</script>

<style lang="scss" scoped>

</style>
