<template>
    <div class="lb-page">
        <h1 class="heading">Event Leaderboard</h1>

        <messagebox
            v-if="isLoading"
            mode="loading"
        />

        <template v-else>
            <router-link :to="{name: 'event_detail', params: {eventId: eventId}}" class="lb-page__event-title">
                <i class="icon-fa icon-fa--inline fas fa-crown"></i>{{event.name}}
            </router-link>

            <table class="leaderboard">
                <tr>
                    <th class="leaderboard__th leaderboard__num">#</th>
                    <th class="leaderboard__th">
                        <i class="icon-fa icon-fa--inline fas fa-user"></i>
                        Username
                    </th>
                    <th class="leaderboard__th">
                        <i class="icon-fa icon-fa--inline fas fa-trophy"></i>
                        Number of Achievements
                    </th>
                    <th class="leaderboard__th">
                        <i class="icon-fa icon-fa--inline fas fa-clock"></i>
                        Hours of Playtime
                    </th>
                    <th class="leaderboard__th">
                        <i class="icon-fa icon-fa--inline fas fa-gamepad"></i>
                        Games Beaten
                    </th>
                </tr>
                <tr
                    v-for="(lbEntry, key) in leaderboard"
                    :key="'lb_'+key"
                    :class="{'leaderboard__tr-inactive': (lbEntry.beaten <= 0)}"
                >
                    <td class="leaderboard__td leaderboard__num">{{key+1}}</td>
                    <td class="leaderboard__td leaderboard__user">
                        <div class="leaderboard__username">
                            <router-link
                                :to="{name: 'user_profile', params: {userId: lbEntry.player}}"
                            >{{users[lbEntry.player].profileName}}</router-link>
                        </div>
                        <div class="leaderboard__userlinks">
                            <a
                                :href="'https://steamcommunity.com/profiles/'+users[lbEntry.player].steamId+'/'"
                                target="_blank"
                                class="leaderboard__user-link"
                            >
                                <i class="icon-fa icon-fa--inline fab fa-steam-symbol"></i>Steam
                            </a>
                            <a
                                :href="'http://steamgifts.com/go/user/'+users[lbEntry.player].steamId"
                                target="_blank"
                                class="leaderboard__user-link"
                            >
                                <i class="icon-fa icon-fa--inline far fa-square"></i>Steamgifts
                            </a>
                        </div>
                    </td>
                    <td class="leaderboard__td">{{+lbEntry.achievements}}</td>
                    <td class="leaderboard__td">
                        <span
                            :title="calcPreciseTime( lbEntry.minutes )"
                        >{{+lbEntry.hours}}</span>
                    </td>
                    <td class="leaderboard__td">
                        <router-link
                            :to="{
                                name: 'event_detail',
                                params: {eventId: event.id},
                                query: {
                                    player: users[lbEntry.player].steamId,
                                    status: 'b_c'
                                }
                            }"
                        >{{+lbEntry.beaten}}</router-link>

                    </td>
                </tr>
                <tr>
                    <td class="leaderboard__td leaderboard__total-num">Total:</td>
                    <td class="leaderboard__td leaderboard__total">{{leaderboard.length}} people</td>
                    <td class="leaderboard__td leaderboard__total">{{totalStats.achievements}}</td>
                    <td class="leaderboard__td leaderboard__total">
                        <span
                            :title="calcPreciseTime( totalStats.minutes )"
                        >{{totalStats.hours}}</span>
                    </td>
                    <td class="leaderboard__td leaderboard__total">{{totalStats.beaten}}</td>
                </tr>
            </table>

            <div
                v-if="showTemplate"
                class="form lb-page__template"
            >
                <label for="lb-template" class="form__label">Markdown format for SG:</label>
                <textarea
                    id="lb-template"
                    class="form__textarea"
                >{{mdTemplate}}</textarea>
            </div>

        </template>

    </div>
</template>

<script>
    import Messagebox from "../components/Messagebox";
    import preciseTime from "../services/preciseTime";

    import {mapGetters} from 'vuex';

    export default {
        name: "LeaderboardPage",
        components: {
            Messagebox
        },
        data: function () {
            return {
                isLoading: true,
            };
        },
        computed: {
            ...mapGetters({
                leaderboard: 'getLeaderboard',
                showTemplate: 'isReadingAnyUserAllowed'
            }),

            eventId: function () {
                return parseInt(this.$route.params.eventId);
            },

            event: function () {
                return this.$store.getters.getEvent(this.eventId);
            },

            users: function () {
                return this.$store.getters.getUsers();
            },

            totalStats: function () {
                let total = {
                    achievements: 0,
                    hours: 0,
                    minutes: 0,
                    beaten: 0
                };

                this.leaderboard.forEach((item) => {
                    total.achievements += item.achievements;
                    total.minutes += item.minutes;
                    total.beaten += item.beaten;
                });

                total.achievements = total.achievements.toFixed(0);
                total.hours = (total.minutes / 60).toFixed(1);
                total.minutes = total.minutes.toFixed(0);
                total.beaten = total.beaten.toFixed(0);

                return total;
            },

            mdTemplate: function () {
                let mdTemplate = 'SG username | Number of achievements | Hours of Playtime | Games beaten\n' +
                    ':-: | :-: | :-: | :-:\n';

                this.leaderboard.forEach((lbEntry) => {
                    if (lbEntry.beaten <= 0)
                        return true;

                    let userName = this.users[lbEntry.player].profileName;

                    mdTemplate += `${userName} | ${+lbEntry.achievements} | ${+lbEntry.hours} | ${+lbEntry.beaten}\n`;
                });

                mdTemplate += `**Total** | **${+this.totalStats.achievements}** | **${+this.totalStats.hours}** | **${+this.totalStats.beaten}**\n`;

                return mdTemplate;
            }
        },
        created() {
            this.$store.dispatch('fetchLeaderboard', this.eventId)
                .finally(() => this.isLoading = false)
        },
        methods: {
            calcPreciseTime(value) {
                return preciseTime(value);
            }
        }
    }
</script>

<style lang="less">
    @import "../assets/mixins";
    @import "../assets/blocks/leaderboard";
    @import "../assets/blocks/form";

    .lb-page{

        &__event-title{
            font-size: 18px;
            display: inline-block;
            margin-bottom: 14px;
        }

        &__template{
            max-width: 800px;
        }
    }
</style>