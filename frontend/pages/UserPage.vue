<template>
    <div class="profile">

        <messagebox
            v-if="isLoading"
            mode="loading"
        />

        <template v-else>
            <div class="profile__top">
                <div class="profile__pic">
                    <img
                        :src="user.avatar"
                        class="profile__img"
                        :alt="user.profileName"
                    />
                    <div class="profile__links">
                        <a
                            :href="user.profileUrl"
                            class="profile__link"
                            target="_blank"
                        ><i class="icon-fa icon-fa--inline fab fa-steam-square"></i>Steam</a>
                        <a
                            :href="`https://steamgifts.com/${user.sgProfileName ? 'user/'+user.sgProfileName : 'go/user/'+user.steamId}`"
                            class="profile__link"
                            target="_blank"
                        ><i class="icon-fa icon-fa--inline far fa-square"></i>Steamgifts</a>
                    </div>
                </div>
                <div class="profile__basic">
                    <div class="profile__name-block">
                        <div class="profile__name">{{user.profileName}}</div>
                        <div v-if="user.sgProfileName && user.profileName !== user.sgProfileName" class="profile__steam-name">{{user.sgProfileName}}</div>
                    </div>
                    <div class="profile__totals">
                        <div class="profile__heading">
                            <i class="icon-fa icon-fa--inline fas fa-chart-pie"></i>
                            Player's total statistics
                        </div>

                        <div class="profile__total">
                            <div class="profile__total-param">Event entries</div>
                            <div class="profile__total-value">{{+totals.entries}}</div>
                        </div>
                        <div class="profile__total">
                            <div class="profile__total-param">Events participated</div>
                            <div class="profile__total-value">{{+totals.events}}</div>
                        </div>
                        <div class="profile__total">
                            <div class="profile__total-param">Achievements taken in all events</div>
                            <div class="profile__total-value">
                                {{totalPlayStats.achievements ? totalPlayStats.achievements : '&mdash;'}}
                            </div>
                        </div>
                        <div class="profile__total">
                            <div class="profile__total-param">Hours played in all events</div>
                            <div class="profile__total-value" :title="totalPlayTimePrecise">
                                {{this.totalPlayStats.playTime ? totalPlayedHours+'h' : '&mdash;'}}
                            </div>
                        </div>
                        <div class="profile__total">
                            <div class="profile__total-param">Games beaten in all events</div>
                            <div class="profile__total-value">
                                {{totalPlayStats.beaten ? totalPlayStats.beaten : '&mdash;'}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="profile__stats text" v-if="totalPlayStats.beaten">
                <div class="profile__heading">
                    <i class="icon-fa icon-fa--inline fas fa-chart-bar"></i>
                    Statistics per event
                </div>

                <event-stats
                    v-for="(eventTotal, index) in totals.byEvent"
                    :key="'stats_'+index"
                    :event="getEvent(eventTotal.event)"
                    :userSteamId="user.steamId"
                    :stats="{
                        achievements: eventTotal.achievements,
                        beaten: eventTotal.beaten,
                        playTime: eventTotal.playTime
                    }"
                />

            </div>

        </template>

    </div>
</template>

<script>
    import {mapActions, mapGetters} from 'vuex';
    import Messagebox from "../components/Messagebox";
    import EventStats from "../components/EventStats";

    export default {
        name: "UserPage",
        components: {
            EventStats,
            Messagebox
        },
        data () {
            return {
                isLoading: true
            };
        },
        computed: {
            ...mapGetters({
                user: 'getUserProfile',
                totals: 'getTotals',
                getEvent: 'getEvent',
                events: 'getShowedEvents'
            }),

            userId: function () {
                return parseInt(this.$route.params.userId);
            },

            totalPlayStats: function () {
                let totalPlaytime = 0, totalAchievements = 0, totalBeaten = 0;

                this.totals.byEvent.forEach(eventTotal => {
                    totalPlaytime += eventTotal.playTime;
                    totalAchievements += eventTotal.achievements;
                    totalBeaten += eventTotal.beaten;
                });

                return {
                    playTime: totalPlaytime,
                    achievements: totalAchievements,
                    beaten: totalBeaten
                };
            },

            totalPlayedHours: function () {
                return (this.totalPlayStats.playTime / 60).toFixed(1);
            },

            totalPlayTimePrecise: function () {
                let hours = parseInt(this.totalPlayStats.playTime / 60);
                let minutes = parseInt(this.totalPlayStats.playTime % 60);

                return `${hours}h ${minutes}m`;
            }
        },
        methods: {
            ...mapActions({
                doFetchUserProfile: 'fetchUserProfile'
            })
        },
        created() {
            this.doFetchUserProfile(this.userId).finally(() => this.isLoading = false)
        }
    }
</script>

<style lang="less">
    @import "../assets/mixins";

    .profile{

        &__top{
            display: flex;
            margin-bottom: 20px;
        }

        &__pic{
            width: 184px;
            margin-right: 20px;
        }

        &__img{
            width: 100%;
            margin-bottom: 10px;
        }

        &__basic{
            flex-grow: 1;
        }

        &__name-block{
            margin-bottom: 20px;
        }

        &__name{
            font-size: 26px;
            font-weight: bold;
            color: @color-dark-blue;

        }

        &__steam-name{

        }

        &__links{

        }

        &__link{
            & + &{
                margin-left: 10px;
            }
        }

        &__totals{
            max-width: 600px;
            margin-bottom: 30px;
            border-left: 3px solid @color-pink;
            padding: 15px;
        }

        &__heading{
            font-size: 18px;
            margin-bottom: 10px;
            padding-left: 8px;
        }

        &__total{
            border-bottom: 1px solid @color-blue;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 8px;
        }

        &__total-param{

        }

        &__total-value{
            font-weight: bold;
            color: @color-pink;
        }
    }

</style>
