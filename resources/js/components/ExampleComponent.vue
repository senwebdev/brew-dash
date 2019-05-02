<template>
    <div class="container">
        <v-container fluid>
            <v-layout row>
                <v-flex class="text-xs-center">
                    <h1>Brew-Dash Bake Off</h1>
                </v-flex>
            </v-layout>
                <br><br><br>
            <v-layout row>

                <v-flex xs3 v-for="(batch, index) in batches" v-bind:key="batch.id" >
                    <JqxBulletChart :width="200" :height="500" :barSize="'35%'" :ranges="ranges" :ticks="ticks" ref="batches" 
                        :title="'Tank '+index" :description="batch.unit" :animationDuration="2"
                        :pointer="pointers[index]" :target="targets[index]" :showToolTip="true" :orientation="'vertical'" />
                    <v-text-field
                        label="Brew Amount" v-model="fillAmounts[index]" @keyup.enter="updateBatch(index+1, fillAmounts[index], 0)"
                    ></v-text-field>
                    <v-text-field
                        label="Fill Amount" v-model="brewAmounts[index]" @keyup.enter="updateBatch(index+1, brewAmounts[index], 1)"
                    ></v-text-field>
                    <v-btn @click="dumpBatch(index+1)">Dump Batch</v-btn>
                    <v-btn @click="kegBatch(index+1)">Keg Batch</v-btn>
                </v-flex>
            </v-layout>
            <br/><br/><br/><br/>
            <v-layout row wrap>
                <v-text-field 
                    label="Source Tank" v-model="sourceTank" 
                ></v-text-field>
                <v-text-field
                    label="Target Tank" v-model="targetTank" 
                ></v-text-field>
                <v-text-field
                    label="Move Amount" v-model="moveAmount" 
                ></v-text-field><br/>
                <v-btn @click="moveBatch(sourceTank, targetTank, moveAmount)">Move</v-btn>
            </v-layout>

        </v-container>
    </div>
</template>

<script>
    import JqxBulletChart from 'jqwidgets-scripts/jqwidgets-vue/vue_jqxbulletchart.vue';
    export default {
        components: {
            JqxBulletChart
        },
        data: function () {
            return {
                batches: [],
                ranges: [{ startValue: 0, endValue: 300, color: 'Grey', opacity: 0.5 }],
                pointers: [{value:0, label:'', size:'75%', color:'Black'}, {value:0, label:'', size:'75%', color:'Black'},{value:0, label:'', size:'75%', color:'Black'},{value:0, label:'', size:'75%', color:'Black'},{value:0, label:'', size:'75%', color:'Black'}],
                targets: [{ value: 0, label: 'Tank Status', size: 6, color: '#c1c1c1' },{ value: 0, label: 'Tank Status', size: 6, color: '#c1c1c1' },{ value: 0, label: 'Tank Status', size: 6, color: '#c1c1c1' },{ value: 0, label: 'Tank Status', size: 6, color: '#c1c1c1' },{ value: 0, label: 'Tank Status', size: 6, color: '#c1c1c1' }],
                ticks: { position: 'far', interval: 50, size: 5 },
                brewAmounts: [], fillAmounts: [], sourceTank: 0, targetTank: 0, moveAmount: 0,
            }
        },
        methods: {
            getBatch() {
                axios.get('http://powerful-everglades-13168.herokuapp.com/show-batch')
                .then((res) => {
                    this.batches = res.data;
                    for(var i=0;i<this.batches.length;i++){
                        this.pointers[i].value = this.batches[i].amount;
                        this.pointers[i].label = 'Tank ' + i;
                        this.targets[i].value = this.batches[i].amount;
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            async updateBatch(tank, amount, direction) {
                var vm = this;
                var index = tank;
                index = index - 1;
                var am = parseFloat(this.pointers[index].value);
                console.log(am);
                if(direction == 1){
                    if(parseFloat(amount) + am > 300) {
                        alert("Tank is full!");
                        am = 300;
                    }
                    am += parseFloat(amount);
                }
                else{
                    if(parseFloat(amount) > am){ 
                        alert("Tank is empty!");
                        am = 0;
                    }
                    am -= parseFloat(amount);
                }
                
                await axios.post('http://powerful-everglades-13168.herokuapp.com/update-batch/' + tank + '/' + amount + '/' + direction)
                .then((res) => {
                    this.getBatch();
                    vm.$refs.batches[index].val(am);
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            async moveBatch(source, target, amount) {
                var vm = this; 
                var sourceAm = parseFloat(this.pointers[source-1].value);
                var targetAm = parseFloat(this.pointers[target-1].value);
                var amount = parseFloat(amount);
                console.log(sourceAm, targetAm, amount);

                await axios.post('http://powerful-everglades-13168.herokuapp.com/move-batch/' + source + '/' + target + '/' + amount)
                .then((res) => {
                    this.getBatch();
                    vm.$refs.batches[source-1].val(sourceAm-amount);
                    vm.$refs.batches[target-1].val(targetAm+amount);
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            async dumpBatch(tank) {
                console.log(tank)
                await axios.post('http://powerful-everglades-13168.herokuapp.com/dump-batch/' + tank)
                .then(response => alert(response.data == 0 ? 'Dumped Successfully!' : 'Dump Failed!'))
                .catch(function (error) {
                    console.log(error);
                });
            },
            async kegBatch(tank) {
                await axios.post('http://powerful-everglades-13168.herokuapp.com/keg-batch/' + tank)
                .then(response => alert(response.data == 1 ? 'Kegged Successfully!' : 'Keg Failed!'))
                .catch(function (error) {
                    console.log(error);
                });
            }
        },
        mounted() {
            this.getBatch();
            
            //this.updateBatch(1, 300, 0)
            //this.moveBatch(1, 2, 200)
            //this.dumpBatch(1);
            //this.kegBatch(1);

        }
    }

</script>

<style>
input{
    width:200px;
}
</style>