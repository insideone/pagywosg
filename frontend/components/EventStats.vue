<template>
    <div class="event-stats">
        <router-link
            :to="{name: 'event_detail', params: {eventId: event.id}}"
            class="event-stats__title"
        >{{event.name}}</router-link>
        <div class="event-stats__dates">
            <i class="icon-fa icon-fa--inline fas fa-calendar-alt"></i>
            {{event.startedAt}} &mdash; {{event.endedAt}}
        </div>

        <div class="event-stats__items">
            <div class="event-stats__overall">
                <router-link
                    :to="{name: 'event_detail', params: {eventId: event.id}, query: {player: userSteamId}}"
                >
                    <i class="icon-fa icon-fa--inline fas fa-stream"></i>See all player's participation
                </router-link>
            </div>
            <div class="event-stats__item">
                <span class="event-stats__number">
                    <i class="icon-fa icon-fa--inline fas fa-trophy"></i>{{stats.achievements}}
                </span>
                achievements taken
            </div>
            <div class="event-stats__item">
                <span class="event-stats__number" :title="precisePlaytime">
                    <i class="icon-fa icon-fa--inline fas fa-clock"></i>{{formattedPlaytime}}
                </span>
                hours played
            </div>
            <div class="event-stats__item">
                <router-link
                    :to="{
                        name: 'event_detail',
                         params: {eventId: event.id},
                         query: { player: userSteamId, status: 'b_c' }
                     }"
                    class="event-stats__number event-stats__number--link"
                >
                    <i class="icon-fa icon-fa--inline fas fa-gamepad"></i>{{stats.beaten}}
                </router-link>
                games beaten
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "EventStats",
        props: {
            event: {
                type: Object,
                default: {
                    description: '',
                    endedAt: '',
                    gameCategories: [],
                    host: 0,
                    id: 0,
                    name: '',
                    participantCount: 0,
                    startedAt: ''
                }
            },
            stats: {
                type: Object,
                default: {
                    achievements: 0,
                    beaten: 0,
                    playTime: 0
                }
            },
            userSteamId: {
                type: String,
                default: ''
            }
        },
        computed: {
            formattedPlaytime: function () {
                return (this.stats.playTime / 60).toFixed(1);
            },

            precisePlaytime: function () {
                let hours = parseInt(this.stats.playTime / 60);
                let minutes = parseInt(this.stats.playTime % 60);

                return `${hours}h ${minutes}m`;
            }
        },
    }
</script>

<style lang="less">
    @import "../assets/mixins";

    .event-stats{
        border: 1px solid @color-blue;
        border-radius: 4px;
        padding: 20px 30px;
        margin-bottom: 30px;
        transition: background-color 0.3s;

        &:hover{
            background: fade(@color-blue, 10%);
        }

        &__title{
            font-size: 20px;
            margin-bottom: 10px;
            display: inline-block;
        }

        &__dates{
            color: @color-gray;
            margin-bottom: 10px;
        }

        &__items{
            display: flex;
            justify-content: flex-end;
            align-items: baseline;
        }

        &__overall{
            margin-right: auto;
            padding-right: 20px;
        }

        &__item{

            & + &{
                margin-left: 20px;
            }
        }

        &__number{
            font-size: 18px;
            font-weight: bold;
            color: @color-blue;
            
            &--link{
                color: @color-pink;
                text-decoration: none;
            }
        }
    }

</style>