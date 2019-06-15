<template>
    <div class="event-detail">

        <!--TODO:-->
        <!--<messagebox-->
            <!--v-if="eventId === 1"-->
        <!--&gt;-->
            <!--Due to timezones/people not being online 24/7 I am still accepting any edits/applications for-->
            <!--<a href="#">April</a>-->
            <!--for the next couple of days (till the April thread is closed).-->
        <!--</messagebox>-->

        <messagebox
            v-if="isLoading"
            mode="loading"
        />

        <messagebox
            v-else-if="errorText"
            mode="error"
        >
            {{errorText}}
        </messagebox>

        <template v-else>
            <div class="event-detail__nav">
                <router-link :to="{name: 'events'}">
                    <i class="icon-fa icon-fa--inline fas fa-long-arrow-alt-left"></i>back
                </router-link>
                <router-link
                    v-if="eventEditAllowed"
                    :to="{name: 'event_edit', params: {eventId: eventId}}"
                >
                    <i class="icon-fa icon-fa--inline fas fa-pencil-alt"></i>Edit
                </router-link>
            </div>

            <div class="event-detail__top-line">

                <div class="event-detail__head-block">
                    <h1 class="heading">{{event.name}}</h1>

                    <div class="event-detail__dates">
                        <i class="icon-fa icon-fa--inline fas fa-calendar-alt"></i>
                        {{event.startedAt}} &mdash; {{event.endedAt}}
                    </div>

                </div>
                <a
                    :href="host.profileUrl"
                    target="_blank"
                    class="event-detail__host"
                    title="The creator of this event"
                >
                    <img
                        :src="host.avatar"
                        :alt="host.profileName"
                        class="event-detail__host-img"
                    />
                    <span class="event-detail__host-name">
                        {{host.profileName}}
                    </span>
                </a>

            </div>

            <div class="event-detail__descr">
                <span class="event-detail__btn-collapse" @click="toggleDescription">
                    <template v-if="isDescrCollapsed">
                        <i class="icon-fa fas fa-plus"></i>
                        Expand description
                    </template>
                    <template v-else>
                        <i class="icon-fa fas fa-minus"></i>
                        Collapse description
                    </template>
                </span>

                <div
                    :class="['event-detail__descr-block', {'_active': !isDescrCollapsed}]"
                >
                    <div class="event-detail__descr-content text" v-html="eventDescriptionCompiled"></div>
                </div>
            </div>

            <div class="event-detail__descr" v-if="eventUnlocksCompiled">
                <div
                    :class="['event-detail__descr-block', {'_active': true}]"
                >
                    <div class="event-detail__descr-content text" v-html="eventUnlocksCompiled"></div>
                </div>
            </div>


            <div class="event-detail__links">
                <router-link
                    :to="{name: 'event_leaderboard', params: {eventId: eventId}}"
                    class="button"
                >
                    <i class="icon-fa icon-fa--inline fas fa-crown"></i>
                    Leaderboard
                </router-link>

                <a
                    v-if="entryCreateAllowed"
                    class="button"
                    @click.prevent="showNewRow(event, true)"
                >
                    <i class="icon-fa icon-fa--inline fas fa-plus-square"></i>
                    Add new game
                </a>
            </div>
            
            <div class="event-detail__table utable">
                <div class="utable__hrow">
                    <div class="utable__hcell utable__col-num">
                        <div class="utable__heading">
                            <i class="icon-fa fas fa-hashtag"></i>
                        </div>
                    </div>
                    <div class="utable__hcell utable__col-player">
                        <div class="utable__heading">
                            <i class="icon-fa icon-fa--inline fas fa-user"></i>
                            Player
                            <a
                                v-if="loggedUser"
                                @click.prevent="setFilterMineEntries"
                                title="Show my entries only"
                                class="utable__link-control utable__link-me"
                            >
                                <i class="icon-fa icon-fa--inline fas fa-arrow-alt-circle-down"></i>me
                            </a>
                        </div>
                        <div class="utable__filter">
                            <input
                                v-model="filter.player"
                                class="utable__input"
                                placeholder="Player name or steamID..."
                            />
                        </div>
                    </div>
                    <div class="utable__hcell utable__col-game">
                        <div class="utable__heading">
                            <i class="icon-fa icon-fa--inline fab fa-steam-square"></i>
                            Game
                        </div>
                        <div class="utable__filter">
                            <input
                                v-model="filter.game"
                                class="utable__input"
                                placeholder="Game title or appID..."
                            />
                        </div>
                    </div>
                    <div class="utable__hcell utable__col-subcat">
                        <div class="utable__heading">
                            <i class="icon-fa icon-fa--inline far fa-flag"></i>
                            Subcategory
                        </div>
                        <div class="utable__filter">
                            <input
                                v-model="filter.category"
                                class="utable__input"
                                placeholder="Subcategory..."
                            />
                        </div>
                    </div>
                    <div class="utable__hcell utable__col-stats">
                        <div class="utable__heading" title="Play stats: achievements and playtime">
                            <i class="icon-fa icon-fa--inline fas fa-trophy"></i>&nbsp;<i class="icon-fa icon-fa--inline far fa-clock"></i>
                            <span class="utable__htext-long">Play stats</span>
                        </div>
                    </div>
                    <div class="utable__hcell utable__col-status">
                        <div class="utable__heading">
                            <i class="icon-fa icon-fa--inline fas fa-clipboard-check"></i>
                            Status
                        </div>
                        <div class="utable__filter">
                            <input
                                v-model="filter.playStatus"
                                class="utable__input"
                                placeholder="Status..."
                            />
                        </div>
                    </div>
                </div>
                <div
                    v-if="eventEditAllowed"
                    class="utable__hrow"
                >
                    <div class="utable__hcell utable__hcell--verify">
                        <span class="utable__show-label">Game Status:</span>

                        <a
                            :class="getClassesForVerifiedOption('gameVerified', null)"
                            @click="changeGameVerifiedFilter(null)"
                        >
                            <i class="icon-fa icon-fa--inline fas fa-stream"></i>All
                        </a>
                        <a
                            :class="getClassesForVerifiedOption('gameVerified', 1)"
                            @click="changeGameVerifiedFilter(1)"
                        >
                            <i class="icon-fa icon-fa--inline fas fa-gamepad"></i>Verified
                        </a>
                        <a
                            :class="getClassesForVerifiedOption('gameVerified', 0)"
                            @click="changeGameVerifiedFilter(0)"
                        >
                            <i class="icon-fa icon-fa--inline fas fa-question"></i>Unverified
                        </a>

                    </div>
                    <div class="utable__hcell utable__hcell--verify">
                        <span class="utable__show-label">Played Status:</span>

                        <a
                            :class="getClassesForVerifiedOption('playStatusVerified', null)"
                            @click="changePlayStatusVerifiedFilter(null)"
                        >
                            <i class="icon-fa icon-fa--inline fas fa-stream"></i>All
                        </a>
                        <a
                            :class="getClassesForVerifiedOption('playStatusVerified', 1)"
                            @click="changePlayStatusVerifiedFilter(1)"
                        >
                            <i class="icon-fa icon-fa--inline fas fa-clipboard-check"></i>Verified
                        </a>
                        <a
                            :class="getClassesForVerifiedOption('playStatusVerified', 0)"
                            @click="changePlayStatusVerifiedFilter(0)"
                        >
                            <i class="icon-fa icon-fa--inline far fa-clipboard"></i>Unverified
                        </a>

                    </div>
                </div>

                <utable-row
                    v-for="(eventEntry, key) in eventEntries"
                    :key="'event_entry_'+eventEntry.id"
                    :event-entry="eventEntry"
                    :i="key"
                    :isMine="loggedUser && eventEntry.player === loggedUser.id"
                    :game-categories="gameCategories"
                    :play-statuses="playStatuses"
                />

                <utable-row-add-new
                    v-if="isNewRowShown"
                    id="event-new-row"
                    :eventId="eventId"
                    :eventEntriesCnt="eventEntriesCnt"
                    :game-categories="gameCategories"
                    :play-statuses="playStatuses"
                    @hide-form="hideNewRow"
                />

                <div
                    v-if="!isNewRowShown && entryCreateAllowed"
                    class="utable__row utable__row--end"
                >
                    <a
                        @click.prevent="showNewRow"
                        class="button"
                    >
                        <i class="icon-fa icon-fa--inline fas fa-plus-square"></i>
                        Add new game
                    </a>
                </div>

            </div>

        </template>

    </div>
</template>

<script>
    import marked from 'marked'

    import Messagebox from '../components/Messagebox.vue'
    import UtableRow from '../components/UtableRow.vue'
    import UtableRowAddNew from '../components/UtableRowAddNew.vue'

    import { mapGetters } from 'vuex'

    export default {
        name: "EventPage",
        components: {
            Messagebox,
            UtableRow,
            UtableRowAddNew
        },
        data: function () {
            return {
                isLoading: true,
                errorText: '',

                isDescrCollapsed: false,
                isNewRowShown: false,

                filter: {
                    player: '',
                    game: '',
                    category: '',
                    playStatus: '',
                    gameVerified: null,
                    playStatusVerified: null
                }
            };
        },
        computed: {
            ...mapGetters({
                entryCreateAllowed: 'isCreatingEntryAllowed'
            }),

            eventEditAllowed: function () {
                return this.$store.getters.isEditingTheEventAllowed(this.eventId);
            },

            playStatuses: function () {
                return this.$store.getters.getPlayStatuses();
            },

            loggedUser: function () {
                return this.$store.getters.getLoggedUser;
            },

            eventId: function () {
                return parseInt(this.$route.params.eventId);
            },

            event: function () {
                return this.$store.getters.getEvent(this.eventId);
            },

            host: function () {
                return this.$store.getters.getUser(this.event.host);
            },

            gameCategories: function () {
                return this.$store.getters.getGameCategories(this.event.gameCategories);
            },

            eventEntries: function () {
                return this.$store.getters.getEventEntries(this.event.entries).filter(entry => {
                    if (this.filter.player) {
                        let
                            user = this.getUser(entry.player),
                            hasFitName = user.profileName.toLowerCase().indexOf(this.filter.player.toLowerCase()) !== -1,
                            hasFitSteamId = user.steamId.toString() === this.filter.player.toString();

                        if (!(hasFitName || hasFitSteamId)) {
                            return false;
                        }
                    }

                    if (this.filter.game) {
                        let
                            game = this.getGame(entry.game),
                            hasFitTitle = game.name.toLowerCase().indexOf(this.filter.game.toLowerCase()) !== -1,
                            hasFitAppId = game.id.toString() === this.filter.game.toString();

                        if (!(hasFitTitle || hasFitAppId)) {
                            return false;
                        }
                    }

                    if (this.filter.category) {
                        let
                            category = this.getGameCategory(entry.category),
                            hasFitName = category.name.toLowerCase().indexOf(this.filter.category.toLowerCase()) !== -1;

                        if (!hasFitName) {
                            return false;
                        }
                    }

                    if (this.filter.playStatus) {
                        let
                            playStatus = this.getPlayStatus(entry.playStatus),
                            hasFitName = playStatus.name.toLowerCase().indexOf(this.filter.playStatus.toLowerCase()) !== -1;

                        if (!hasFitName) {
                            return false;
                        }
                    }

                    let resultVerified = true;
                    let resultPlayStatusVerified = true;

                    if (this.filter.gameVerified !== null) {
                        resultVerified = entry.verified === (this.filter.gameVerified === 1);
                    }

                    if (this.filter.playStatusVerified !== null) {
                        resultPlayStatusVerified = entry.playStatusVerified === (this.filter.playStatusVerified === 1);
                    }

                    return (resultVerified && resultPlayStatusVerified);

                });
            },

            eventEntriesCnt: function () {
                return this.event.entries.length;
            },

            eventDescriptionCompiled: function () {
                return this.renderMarkdown(this.event.description);
            },
            eventUnlocksCompiled: function () {
                return this.renderMarkdown(this.event.unlocks);
            }
        },
        created () {
            Promise.all([
                this.$store.dispatch('fetchEvent', this.eventId),
                this.$store.dispatch('fetchPlayStatuses')
            ])
                .then()
                .catch((e) => {
                    this.errorText = e;
                })
                .finally(() => {
                    this.isLoading = false;
                });

            this.filter.player = this.$route.query.player;

        },
        methods: {
            renderMarkdown(text) {
                if (!text) {
                    return '';
                }

                return marked(text, {sanitize: true, breaks: true});
            },

            getUser(id) {
                return this.$store.getters.getUser(id);
            },

            getGame(id) {
                return this.$store.getters.getGame(id);
            },

            getGameCategory(id) {
                return this.$store.getters.getGameCategory(id);
            },

            getPlayStatus(id) {
                return this.$store.getters.getPlayStatus(id);
            },

            toggleDescription(){
                let self = this;
                let descrBlock = document.querySelector('.event-detail__descr-block');
                let descrContent = descrBlock.querySelector('.event-detail__descr-content');

                if (this.isDescrCollapsed)
                {
                    descrBlock.classList.add('_animating');
                    descrBlock.style.maxHeight = descrContent.offsetHeight + 'px';

                    descrBlock.addEventListener('transitionend', () => {
                        descrBlock.classList.add('_active');
                        descrBlock.classList.remove('_animating');
                        descrBlock.style.maxHeight = 'none';

                        self.isDescrCollapsed = false;
                    }, {once: true});
                }
                else
                {
                    descrBlock.style.maxHeight = descrContent.offsetHeight + 'px';

                    setTimeout(() => {
                        descrBlock.classList.add('_animating');
                        descrBlock.classList.remove('_active');
                        descrBlock.removeAttribute('style');

                        descrBlock.addEventListener('transitionend', () => {
                            descrBlock.classList.remove('_animating');

                            self.isDescrCollapsed = true;
                        }, {once: true})

                    }, 25);
                }
            },

            showNewRow($event, scrollToNewRow) {
                scrollToNewRow = scrollToNewRow || false;

                this.isNewRowShown = true;

                if (scrollToNewRow)
                {
                    setTimeout(() => {
                        document.getElementById('event-new-row').scrollIntoView({behavior: 'auto'});
                    }, 25);
                }
            },

            hideNewRow() {
                this.isNewRowShown = false;
            },

            changeGameVerifiedFilter(value) {
                this.filter.gameVerified = value;
            },

            changePlayStatusVerifiedFilter(value) {
                this.filter.playStatusVerified = value;
            },

            getClassesForVerifiedOption(type, value) {
                let classes = ['utable__show-option'];

                if (this.filter[type] === value)
                    classes.push('utable__show-option--selected');

                return classes;
            },

            setFilterMineEntries() {
                this.filter.player = this.loggedUser.steamId;
            }
        }
    }
</script>

<style lang="less">
    @import "../assets/mixins";

    @import "../assets/blocks/form";
    @import "../assets/blocks/utable";

    .event-detail{
        padding-bottom: 50px;

        &__nav{
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        &__top-line{
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        &__head-block{
            flex-grow: 1;
        }

        &__host{
            box-sizing: border-box;
            padding-top: 20px;
            min-width: 80px;
            flex-shrink: 0;
            margin-left: 16px;
            text-align: center;
            text-decoration: none;
        }

        &__host-img{
            width: 80px;
            height: 80px;
            display: block;
            margin: 0 auto 6px;
        }

        &__host-name{
            color: @color-blue;
            white-space: nowrap;
        }

        &__host:hover &__host-name{
            color: @color-red;
        }

        &__dates{
            font-size: 12px;
            color: @color-gray;
        }

        &__descr{
            border-top: 1px solid @color-light-blue;
            border-bottom: 1px solid @color-light-blue;
            padding: 24px 0 10px;
            margin-bottom: 16px;
            box-sizing: border-box;
            position: relative;
        }

        &__descr-block{
            max-height: 0;
            overflow: hidden;

            &._animating{
                transition: max-height 0.4s ease;
            }

            &._active{
                max-height: none;
            }
        }

        &__descr-content{

        }

        &__btn-collapse{
            font-size: 11px;
            color: @color-gray;
            border-bottom: 1px dotted @color-gray;
            display: inline-block;
            position: absolute;
            top: 4px;
            left: 50%;
            transform: translateX(-50%);
            cursor: pointer;
            transition: border-color 0.3s;

            &:hover{
                border-bottom-color: rgba(0, 0, 0, 0);
            }
        }
        
        &__links{
            padding: 10px;
            display: flex;
            justify-content: space-between;
        }
    }

</style>