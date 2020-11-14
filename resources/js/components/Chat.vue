<template>
    <chat-window
        ref="chat"
        :currentUserId="currentUserId"
        :rooms="rooms"
        :messages="messages"
        :messagesLoaded="true"
        :menuActions="menuActions"
        :messageActions="messageActions "
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
        props: ['authUser', 'threads'],
        data() {
            return {
                rooms: [],
                menuActions: [
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
                messageActions: [
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
                messages: [],
                currentUserId: this.authUser.id,
                cachedMessages: {},
                activePeer: false,
                typingTimer: false,
                channels: []
            }
        },
        created() {
            this.createRoomsFromThreads()
        },
        mounted() {
            this.threads.forEach(thread => {
                Echo.join(`thread.${thread.id}`)
                    .listen('.NewMessage', this.onNewMessage)
                    .listenForWhisper('typing', this.onUserWhisper)
                    .here((users) => {
                        users.forEach(user => {
                            this.setUserStatus(thread.id, user, true)
                        })
                    })
                    .joining((user) => {
                        this.setUserStatus(thread.id, user, true)
                    })
                    .leaving((user) => {
                        this.setUserStatus(thread.id, user, false)
                    });
            });
        },
        methods: {
            setUserStatus(roomId, currentUser, isHere) {
                const room = this.rooms.find(room => room.roomId === roomId)
                const user = room.users.find(user => user._id === currentUser.id)
                user.status.state = isHere ? 'online' : 'offline'
            },
            findRoomByRoomId(roomId) {
                return this.rooms.filter(room => {
                    return room.roomId === roomId
                })[0]
            },
            onUserWhisper(e) {
                const room = this.findRoomByRoomId(e.thread)
                room.typingUsers.push(e.user)

                if (this.typingTimer) clearTimeout(this.typingTimer)

                this.typingTimer = setTimeout(() => {
                    room.typingUsers.splice(room.typingUsers.indexOf(e.user))
                }, 1000)

            },
            onNewMessage(data) {

                const activeRoomId = this.$refs.chat.$children[0].$data.selectedRoomId

                if (!Array.isArray(this.cachedMessages[data.message.thread_id])) {
                    this.cachedMessages[data.message.thread_id] = []
                }

                this.cachedMessages[data.message.thread_id].push({
                    _id: data.message.id,
                    content: data.message.body,
                    sender_id: data.sender.id,
                    username: data.sender.name,
                    date: new Date().toISOString().slice(0, 10),
                    timestamp: new Date().toLocaleTimeString(),
                    seen: false,
                    disable_actions: false,
                    disable_reactions: false,
                })

                this.setRoomsLastMessage(data);

                if( activeRoomId !== data.message.thread_id)
                {
                    this.setRoomsUnreadCount(data.message.thread_id, 1)
                }


            },
            setRoomsLastMessage(data){

                const createdDate = new Date(data.message.created_at);
                const room = this.rooms.find(room=>room.roomId === data.message.thread_id)
                room.lastMessage = {
                        content: data.message.body,
                        sender_id: data.sender.id,
                        timestamp: createdDate.getDate() + '/' + createdDate.getMonth() + '/' + createdDate.getFullYear(),
                        date: createdDate.getTime(),
                        seen: false,
                        new: true
                }

            },
            createRoomsFromThreads() {

                this.threads.forEach(thread => {

                    const users = thread.participants.map(participant => {
                        return {
                            _id: participant.user_id,
                            username: participant.user.name,
                            status: {
                                state: participant.user_id === this.authUser.id ? 'online' : 'offline',
                                last_changed: participant.last_read
                            }
                        };
                    });

                    const createdDate = new Date(thread.last_message.created_at);
                    this.rooms.push({
                        roomId: thread.id,
                        roomName: thread.subject,
                        unreadCount: thread.unreadMessages,
                        lastMessage: {
                            content: thread.last_message.body,
                            sender_id: thread.last_message.user_id,
                            timestamp: createdDate.getDate() + '/' + createdDate.getMonth() + '/' + createdDate.getFullYear(),
                            date: createdDate.getTime(),
                            seen: false,
                            new: true
                        },
                        users: users,
                        typingUsers: []
                    })
                })
            },
            async getMessages(args) {

                const roomId = args.room.roomId

                if (this.cachedMessages.hasOwnProperty(roomId) && this.cachedMessages[roomId].length > 1) {
                    this.messages = this.cachedMessages[roomId];
                    this.setRoomsUnreadCount(roomId)
                    return
                }

                this.cachedMessages[roomId] = await this.getLatestMessagesForThread(roomId)
                this.setRoomsUnreadCount(roomId)
                this.messages = this.cachedMessages[roomId]
            },
            setRoomsUnreadCount(roomId, count = null) {

                if (count) {
                    this.rooms.find(room => room.roomId === roomId).unreadCount++
                } else {
                    this.rooms.find(room => room.roomId === roomId).unreadCount = null
                }

            },
            getLatestMessagesForThread(roomId) {
                return axios.get('threads/' + roomId).then(response => {

                    const messages = [];
                    response.data.messages.forEach(message => {
                        messages.push({
                            _id: message.id,
                            content: message.body,
                            sender_id: message.user.id,
                            username: message.user.name,
                            date: new Date().toISOString().slice(0, 10),
                            timestamp: new Date().toLocaleTimeString(),
                            seen: false,
                            disable_actions: false,
                            disable_reactions: false,
                        })
                    })

                    return messages;

                }).catch(error => {
                    return [];
                })
            },
            notifyParticipants(args) {

                if (!args.message) return;

                let channel = Echo.join('thread.' + args.roomId)
                setTimeout(() => {
                    channel.whisper('typing', {
                        thread: args.roomId,
                        user: this.authUser.id,
                        name: this.authUser.name,
                    })
                }, 300)

            },
            sendMessage(args) {
                axios.post('/threads/' + args.roomId, {
                    'message': args.content
                }).then(resp => {

                    this.messages.push({
                        _id: Math.random(),
                        content: args.content,
                        sender_id: this.authUser.id,
                        username: this.authUser.name,
                        date: new Date().toISOString().slice(0, 10),
                        timestamp: new Date().toLocaleTimeString(),
                        seen: false,
                        disable_actions: false,
                        disable_reactions: false,
                    })

                    this.setRoomsLastMessage(resp.data)
                });
            }
        },

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
