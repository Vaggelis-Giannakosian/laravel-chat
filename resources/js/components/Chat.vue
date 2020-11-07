<template>
        <div class="container">

            <h2>Conversation With Socket</h2>

            <form action="" @submit.prevent="sendMessage">

                <div class="form-group">
                    <input type="text" class="form-control" v-model="msg">
                </div>

                <div class="form-group text-right" >
                    <button type="submit" class="btn btn-outline-primary" >Αποστολή</button>
                </div>

            </form>

            <ul>
                <li v-for="message in messages" :class="{'own-message': ownsMessage(message) }">
                    <span> {{ message.message}}</span>
                </li>
            </ul>
        </div>
</template>

<script>

    export default {
        props: ['authUser','receiverId'],
        name: "Chat",
        data(){
            return {
                messages:[],
                msg:''
            }
        },
        mounted(){
            // this.listenForBroadcast();

            let channel = Echo.private('message.user.' + this.authUser.id)
            channel.listen('.UserEvent',(data)=>{
                this.messages.push(data)
            })
        },
        methods:{
            ownsMessage(message){
                return message.sender.name === this.authUser.name
            },
            sendMessage(){

                this.pushNewMessage()

                axios.post('/messages',{
                    'userId':this.receiverId,
                    'message': this.msg
                }).then(resp=>console.log(resp));

                this.msg = '';
            },
            pushNewMessage(){
                this.messages.push({
                    'sender': {'name' : this.authUser.name},
                    'message':this.msg
                })
            }
        }
    }
</script>

<style>
    li{
        list-style: none;
    }

    li.own-message{
        text-align: right;
    }

    span{
        padding: 10px 18px;
        background: #a0a0a0;
        color: white;
        border-radius: 20px;
        margin: 15px 0;
        display: inline-block;
        line-height: 1;
    }

    .own-message span{
        background: #328bd4;
    }

</style>
