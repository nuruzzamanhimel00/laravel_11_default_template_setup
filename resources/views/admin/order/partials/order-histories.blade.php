<div class="container-inner">
    <div class="wrapper">
      <h1> Status History</h1>
      <ul class="sessions">
        @if($sale->order_statuses->count() > 0)
            @foreach ($sale->order_statuses as  $order_status)
                <li>
                    <div class="time"><strong>{{$order_status->created_formate}}</strong> </div>

                    <h5><b> {{ucwords($order_status->title)}}</b> - Created By: {{$order_status->user->full_name}}</h5>
                    <p>{{$order_status->message}}</p>
                </li>
            @endforeach


        @endif

      </ul>
    </div>
</div>
@push('style')
<style>
.wrapper ul, .wrapper li {
  list-style: none;
  /* padding: 0; */
}

.container-inner {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 0 1rem;
}

.wrapper {
  background: #eaf6ff;
  padding: 2rem;
  border-radius: 15px;
}

.wrapper h1 {
  font-size: 1.1rem;
  font-family: sans-serif;
}

.wrapper .sessions {
  margin-top: 2rem;
  border-radius: 12px;
  position: relative;
  padding: 0px;
}

.wrapper li {
  padding-bottom: 0.5rem;
  border-left: 1px solid #abaaed;
  position: relative;
  padding-left: 20px;
  margin-left: 10px;
}

.wrapper li:last-child {
  border: 0px;
  padding-bottom: 0;
}

.wrapper li:before {
  content: '';
  width: 15px;
  height: 15px;
  background: white;
  border: 1px solid #4e5ed3;
  box-shadow: 3px 3px 0px #bab5f8;
  border-radius: 50%;
  position: absolute;
  left: -10px;
  top: 0px;
}

.wrapper .time {
  color: #2a2839;
  font-family: 'Poppins', sans-serif;
  font-weight: 500;
}

@media screen and (min-width: 601px) {
  .wrapper .time {
    font-size: .9rem;
  }
}

@media screen and (max-width: 600px) {
 .wrapper .time {
    margin-bottom: .3rem;
    font-size: 0.85rem;
  }
}

.wrapper p {
  color: #4f4f4f;
  font-family: sans-serif;
  line-height: 1.5;
  margin-top: 0.4rem;
}

@media screen and (max-width: 600px) {
  .wrapper p {
    font-size: .9rem;
  }
}

</style>
@endpush
@push('script')
@endpush
