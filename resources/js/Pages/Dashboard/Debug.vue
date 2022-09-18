<template>
</template>

<script>
export default{
    data(){
        return{
            load: 1
        }
    },
    mounted(){

        $link.init('tEUbveRUMaz8EQmwyDtjajSf63Q5lX','TUY7cklJ')

        let answered = $link.quiz.answered();

        console.log(answered);

        answered.push(2)
        answered = $link.quiz.answered(answered)

     

        /*
        $link.init('qIqUjnAEuPcr6c739LIx0VpjY2umWE','ubM1NUAj')

        $link.log('quiz_answer', {
            question: '¿Cuál de las siguientes afirmaciones es falsa?',
            answer: 'ABC',
            is_correct: false
        })

        $link.log('quiz_answer', {
            question: '¿Cuál de las siguientes afirmaciones es falsa?',
            answer: 'DEF',
            is_correct: true
        })

        $link.log('quiz_answer', {
            question: '¿Cuál de las siguientes afirmaciones es correcta?',
            answer: 'XYZ',
            is_correct: false
        })
        */

    }
}
</script>