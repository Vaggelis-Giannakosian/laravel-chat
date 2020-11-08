<template>
    <chat-window :currentUserId="currentUserId"
                 :rooms="rooms"
                 :messages="messages"
                 :messagesLoaded="true"
                 :menuActions="menuActions"
                 :messageActions ="messageActions "
                 :loadingRooms="false"

                 @fetchMessages="getMessages"
                 @typingMessage="typingMessage"
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
                styles:{
                    general: {
                        color: '#0a0a0a',
                        backgroundInput: '#fff',
                        colorPlaceholder: '#9ca6af',
                        colorCaret: '#1976d2',
                        colorSpinner: '#333',
                        borderStyle: '1px solid #e1e4e8',
                        backgroundScrollIcon: '#fff'
                    },

                    container: {
                        border: 'none',
                        borderRadius: '4px'
                    },

                    header: {
                        background: '#fff',
                        colorRoomName: '#0a0a0a',
                        colorRoomInfo: '#9ca6af'
                    },

                    footer: {
                        background: '#f8f9fa',
                        borderStyleInput: '1px solid #e1e4e8',
                        borderInputSelected: '#1976d2',
                        backgroundReply: 'rgba(0, 0, 0, 0.08)'
                    },

                    content: {
                        background: '#f8f9fa'
                    },

                    sidemenu: {
                        background: '#fff',
                        backgroundHover: '#f6f6f6',
                        backgroundActive: '#e5effa',
                        colorActive: '#1976d2',
                        borderColorSearch: '#e1e5e8'
                    },

                    dropdown: {
                        background: '#fff',
                        backgroundHover: '#f6f6f6'
                    },

                    message: {
                        background: '#fff',
                        backgroundMe: '#ccf2cf',
                        color: '#0a0a0a',
                        colorStarted: '#9ca6af',
                        backgroundDeleted: '#dadfe2',
                        colorDeleted: '#757e85',
                        colorUsername: '#9ca6af',
                        colorTimestamp: '#828c94',
                        backgroundDate: '#e5effa',
                        colorDate: '#505a62',
                        backgroundReply: 'rgba(0, 0, 0, 0.08)',
                        colorReplyUsername: '#0a0a0a',
                        colorReply: '#6e6e6e',
                        backgroundImage: '#ddd',
                        colorNewMessages: '#1976d2',
                        backgroundReaction: '#eee',
                        borderStyleReaction: '1px solid #eee',
                        backgroundReactionHover: '#fff',
                        borderStyleReactionHover: '1px solid #ddd',
                        colorReactionCounter: '#0a0a0a',
                        backgroundReactionMe: '#cfecf5',
                        borderStyleReactionMe: '1px solid #3b98b8',
                        backgroundReactionHoverMe: '#cfecf5',
                        borderStyleReactionHoverMe: '1px solid #3b98b8',
                        colorReactionCounterMe: '#0b59b3'
                    },

                    markdown: {
                        background: 'rgba(239, 239, 239, 0.7)',
                        border: 'rgba(212, 212, 212, 0.9)',
                        color: '#e01e5a',
                        colorMulti: '#0a0a0a'
                    },

                    room: {
                        colorUsername: '#0a0a0a',
                        colorMessage: '#67717a',
                        colorTimestamp: '#a2aeb8',
                        colorStateOnline: '#4caf50',
                        colorStateOffline: '#ccc',
                        backgroundCounterBadge: '#0696c7',
                        colorCounterBadge: '#fff'
                    },

                    emoji: {
                        background: '#fff'
                    },

                    icons: {
                        search: '#9ca6af',
                        add: '#1976d2',
                        toggle: '#0a0a0a',
                        menu: '#0a0a0a',
                        close: '#9ca6af',
                        closeImage: '#fff',
                        file: '#1976d2',
                        paperclip: '#1976d2',
                        closeOutline: '#000',
                        send: '#1976d2',
                        sendDisabled: '#9ca6af',
                        emoji: '#1976d2',
                        emojiReaction: '#828c94',
                        document: '#1976d2',
                        pencil: '#9e9e9e',
                        checkmark: '#0696c7',
                        eye: '#fff',
                        dropdownMessage: '#fff',
                        dropdownMessageBackground: 'rgba(0, 0, 0, 0.25)',
                        dropdownScroll: '#0a0a0a'
                    }
                },
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
                cachedMessages:{}
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
                    {
                        _id: 7891,
                        content: 'message 1',
                        sender_id: 1234,
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
                    }
                ];
            },
            typingMessage(){
                console.log('typing')
            },
            sendMessage(args){

                axios.post('/messages',{
                    'userId':this.receiverId,
                    'message': args.content
                }).then(resp=>console.log(resp));

                this.messages.push({
                    _id: 7888,
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

            let channel = Echo.private('message.user.' + this.authUser.id)
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
