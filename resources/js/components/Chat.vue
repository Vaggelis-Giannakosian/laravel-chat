<template>
    <chat-window :currentUserId="currentUserId"
                 :rooms="rooms"
                 :messages="messages"
                 :messagesLoaded="true"
                 :menuActions="menuActions"
                 :messageActions ="messageActions "
                 :loadingRooms="false"

                 @fetchMessages="getMessages"
                 @typingMessage="notifyParticipants"
                 @sendMessage="sendMessage"
    />
</template>

<script>
    import ChatWindow from 'vue-advanced-chat'
    import 'vue-advanced-chat/dist/vue-advanced-chat.css'

    export default {
        components: {
            ChatWindow
        },
        props: ['authUser','receiverId'],
        data() {
            return {
                rooms: [
                    {
                        roomId: 1,
                        roomName: 'Room 1',
                        unreadCount: 4,
                        lastMessage: {
                            content: 'Last message received',
                            sender_id: 1234,
                            username: 'John Doe',
                            timestamp: '10:20',
                            date: 123242424,
                            seen: false,
                            new: true
                        },
                        users: [
                            {
                                _id: 1234,
                                username: 'John Doe',
                                status: {
                                    state: 'online',
                                    last_changed: 'today, 14:30'
                                }
                            },
                            {
                                _id: 4321,
                                username: 'John Snow',
                                status: {
                                    state: 'online',
                                    last_changed: '14 July, 20:00'
                                }
                            }
                        ],
                        typingUsers: [ 4321 ]
                    },
                ],
                menuActions:[
                    {
                        name: 'inviteUser',
                        title: 'Invite User'
                    },
                    {
                        name: 'removeUser',
                        title: 'Remove User'
                    },
                    {
                        name: 'deleteRoom',
                        title: 'Delete Room'
                    }
                ],
                messageActions:[
                    {
                        name: 'addMessageToFavorite',
                        title: 'Add To Favorite'
                    },
                    {
                        name: 'shareMessage',
                        title: 'Share Message'
                    },
                    {
                        name: 'replyMessage',
                        title: 'Reply'
                    },
                    {
                        name: 'editMessage',
                        title: 'Edit Message',
                        onlyMe: true
                    },
                    {
                        name: 'deleteMessage',
                        title: 'Delete Message',
                        onlyMe: true
                    }
                ],
                messages: this.initializeMessages(),
                currentUserId: 1234,
                cachedMessages:{},
                activePeer:false,
                typingTimer:false
            }
        },
        methods:{
            getMessages(args){

                const roomId = args.room.roomId

                if(this.cachedMessages.hasOwnProperty(roomId))
                {
                    this.messages = this.cachedMessages[roomId];
                }

                this.cachedMessages[roomId] =  this.initializeMessages()
                this.messages = this.cachedMessages[roomId]
            },
            initializeMessages(){
                return [
                    {
                        _id: 7890,
                        content: 'message 1',
                        sender_id: 4566,
                        username: 'John Doe',
                        date: '13 Octomber',
                        timestamp: '10:20',
                        seen: true,
                        disable_actions: false,
                        disable_reactions: false,
                        file: {
                            name: 'My File',
                            size: 67351,
                            type: 'jpg',
                            url: 'https://thumbs.dreamstime.com/b/mockup-iphone-screen-background-have-png-isolated-various-applications-158473491.jpg'
                        },
                        reactions: {
                            wink: [
                                1234, // USER_ID
                                4321
                            ],
                            laughing: [
                                1234
                            ]
                        }
                    },
                ];
            },
            notifyParticipants(){
                console.log('typing')
                let channel = Echo.private('message.user')

                setTimeout( () => {
                    channel.whisper('typing', {
                        user: 'foobar',
                        typing: true
                    })
                }, 300)

            },
            sendMessage(args){

                axios.post('/messages',{
                    'userId':this.receiverId,
                    'message': args.content
                }).then(resp=>console.log(resp));

                this.messages.push({
                    _id: Math.random(),
                    content: args.content,
                    sender_id: 1234,
                    username: 'John Doe',
                    date: '13 Octomber',
                    timestamp: '10:20',
                    seen: false,
                    disable_actions: false,
                    disable_reactions: false,
                })
            }
        },
        mounted(){

            let channel = Echo.private('message.user')
            channel.listen('.NewMessage',(data)=>{
                console.log(data)
                this.messages.push({
                    _id: Math.random(),
                    content: data.message,
                    sender_id: 4321,
                    username: 'John Doe',
                    date: '13 Octomber',
                    timestamp: '10:20',
                    seen: false,
                    disable_actions: false,
                    disable_reactions: false,
                })
            }).listenForWhisper('typing', (e) => {
                console.log(e)

                // this.activePeer = e;
                //
                // if(this.typingTimer) clearTimeout(this.typingTimer)
                //
                // this.typingTimer = setTimeout(()=>{
                //     this.activePeer = false;
                // })

            })

        }
    }
</script>

<!--<template>-->
<!--        <div class="container">-->

<!--            <h2>Conversation With Socket</h2>-->

<!--            <form action="" @submit.prevent="sendMessage">-->

<!--                <div class="form-group">-->
<!--                    <input type="text" class="form-control" v-model="msg">-->
<!--                </div>-->

<!--                <div class="form-group text-right" >-->
<!--                    <button type="submit" class="btn btn-outline-primary" >Αποστολή</button>-->
<!--                </div>-->

<!--            </form>-->

<!--            <ul>-->
<!--                <li v-for="message in messages" :class="{'own-message': ownsMessage(message) }">-->
<!--                    <span> {{ message.message}}</span>-->
<!--                </li>-->
<!--            </ul>-->
<!--        </div>-->
<!--</template>-->

<!--<script>-->

<!--    export default {-->
<!--        props: ['authUser','receiverId'],-->
<!--        name: "Chat",-->
<!--        data(){-->
<!--            return {-->
<!--                messages:[],-->
<!--                msg:''-->
<!--            }-->
<!--        },-->
<!--        mounted(){-->
<!--            // this.listenForBroadcast();-->

<!--            let channel = Echo.private('message.user.' + this.authUser.id)-->
<!--            channel.listen('.NewMessage',(data)=>{-->
<!--                this.messages.push(data)-->
<!--            })-->
<!--        },-->
<!--        methods:{-->
<!--            ownsMessage(message){-->
<!--                return message.sender.name === this.authUser.name-->
<!--            },-->
<!--            sendMessage(){-->

<!--                this.pushNewMessage()-->

<!--                axios.post('/messages',{-->
<!--                    'userId':this.receiverId,-->
<!--                    'message': this.msg-->
<!--                }).then(resp=>console.log(resp));-->

<!--                this.msg = '';-->
<!--            },-->
<!--            pushNewMessage(){-->
<!--                this.messages.push({-->
<!--                    'sender': {'name' : this.authUser.name},-->
<!--                    'message':this.msg-->
<!--                })-->
<!--            }-->
<!--        }-->
<!--    }-->
<!--</script>-->

<!--<style scoped>-->
<!--    li{-->
<!--        list-style: none;-->
<!--    }-->

<!--    li.own-message{-->
<!--        text-align: right;-->
<!--    }-->

<!--    span{-->
<!--        padding: 10px 18px;-->
<!--        background: #a0a0a0;-->
<!--        color: white;-->
<!--        border-radius: 20px;-->
<!--        margin: 15px 0;-->
<!--        display: inline-block;-->
<!--        line-height: 1;-->
<!--    }-->

<!--    .own-message span{-->
<!--        background: #328bd4;-->
<!--    }-->

<!--</style>-->
