<template>

   <content-loader v-if="type=='grid'" viewBox="0 0 820 450" width="100%" :primaryColor="primaryColor" :secondaryColor="secondaryColor">
      <rect x="10" y="10" rx="5" ry="5" width="260" height="140" />
      <rect x="280" y="10" rx="5" ry="5" width="260" height="280" />
      <rect x="550" y="10" rx="5" ry="5" width="260" height="140" />
      <rect x="10" y="160" rx="5" ry="5" width="260" height="280" />
      <rect x="280" y="300" rx="5" ry="5" width="260" height="140" />
      <rect x="550" y="160" rx="5" ry="5" width="260" height="280" />
    </content-loader>

<content-loader v-if="type=='table'"
    width="100%"
    viewBox="0 0 1000 550"
    :primaryColor="primaryColor"
    :secondaryColor="secondaryColor"
  >
    <rect x="51" y="45" rx="3" ry="3" width="906" height="17" />
    <circle cx="879" cy="123" r="11" />
    <circle cx="914" cy="123" r="11" />
    <rect x="104" y="115" rx="3" ry="3" width="141" height="15" />
    <rect x="305" y="114" rx="3" ry="3" width="299" height="15" />
    <rect x="661" y="114" rx="3" ry="3" width="141" height="15" />
    <rect x="55" y="155" rx="3" ry="3" width="897" height="2" />
    <circle cx="880" cy="184" r="11" />
    <circle cx="915" cy="184" r="11" />
    <rect x="105" y="176" rx="3" ry="3" width="141" height="15" />
    <rect x="306" y="175" rx="3" ry="3" width="299" height="15" />
    <rect x="662" y="175" rx="3" ry="3" width="141" height="15" />
    <rect x="56" y="216" rx="3" ry="3" width="897" height="2" />
    <circle cx="881" cy="242" r="11" />
    <circle cx="916" cy="242" r="11" />
    <rect x="106" y="234" rx="3" ry="3" width="141" height="15" />
    <rect x="307" y="233" rx="3" ry="3" width="299" height="15" />
    <rect x="663" y="233" rx="3" ry="3" width="141" height="15" />
    <rect x="57" y="274" rx="3" ry="3" width="897" height="2" />
    <circle cx="882" cy="303" r="11" />
    <circle cx="917" cy="303" r="11" />
    <rect x="107" y="295" rx="3" ry="3" width="141" height="15" />
    <rect x="308" y="294" rx="3" ry="3" width="299" height="15" />
    <rect x="664" y="294" rx="3" ry="3" width="141" height="15" />
    <rect x="58" y="335" rx="3" ry="3" width="897" height="2" />
    <circle cx="881" cy="363" r="11" />
    <circle cx="916" cy="363" r="11" />
    <rect x="106" y="355" rx="3" ry="3" width="141" height="15" />
    <rect x="307" y="354" rx="3" ry="3" width="299" height="15" />
    <rect x="663" y="354" rx="3" ry="3" width="141" height="15" />
    <rect x="57" y="395" rx="3" ry="3" width="897" height="2" />
    <circle cx="882" cy="424" r="11" />
    <circle cx="917" cy="424" r="11" />
    <rect x="107" y="416" rx="3" ry="3" width="141" height="15" />
    <rect x="308" y="415" rx="3" ry="3" width="299" height="15" />
    <rect x="664" y="415" rx="3" ry="3" width="141" height="15" />
    <rect x="55" y="453" rx="3" ry="3" width="897" height="2" />
    <rect x="51" y="49" rx="3" ry="3" width="2" height="465" />
    <rect x="955" y="49" rx="3" ry="3" width="2" height="465" />
    <circle cx="882" cy="484" r="11" />
    <circle cx="917" cy="484" r="11" />
    <rect x="107" y="476" rx="3" ry="3" width="141" height="15" />
    <rect x="308" y="475" rx="3" ry="3" width="299" height="15" />
    <rect x="664" y="475" rx="3" ry="3" width="141" height="15" />
    <rect x="55" y="513" rx="3" ry="3" width="897" height="2" />
    <rect x="52" y="80" rx="3" ry="3" width="906" height="17" />
    <rect x="53" y="57" rx="3" ry="3" width="68" height="33" />
    <rect x="222" y="54" rx="3" ry="3" width="149" height="33" />
    <rect x="544" y="55" rx="3" ry="3" width="137" height="33" />
    <rect x="782" y="56" rx="3" ry="3" width="72" height="33" />
    <rect x="933" y="54" rx="3" ry="3" width="24" height="33" />
  </content-loader >

    <content-loader v-if="type=='pie'" :primaryColor="primaryColor" :secondaryColor="secondaryColor" width="100%" viewBox="0 0 450 820">
      <rect x="100" y="5" rx="0" ry="0" width="200" height="15" />
      <circle cx="140" cy="110" r="70" />
      <rect x="230" y="50" rx="0" ry="0" width="7" height="7" />
      <rect x="250" y="50" rx="0" ry="0" width="30" height="7" />
      <rect x="230" y="64" rx="0" ry="0" width="7" height="7" />
      <rect x="250" y="64" rx="0" ry="0" width="30" height="7" />
      <rect x="230" y="78" rx="0" ry="0" width="7" height="7" />
      <rect x="250" y="78" rx="0" ry="0" width="30" height="7" />
      <rect x="230" y="92" rx="0" ry="0" width="7" height="7" />
      <rect x="250" y="92" rx="0" ry="0" width="30" height="7" />
    </content-loader>

    <content-loader v-if="type=='list'"
      width="100%"
      :primaryColor="primaryColor" :secondaryColor="secondaryColor"
    >
      <rect x="70" y="15" rx="5" ry="5" width="300" height="15" />
      <rect x="70" y="39" rx="5" ry="5" width="220" height="9" />
      <rect x="20" y="10" rx="0" ry="0" width="40" height="40" />
   </content-loader>

    <content-loader v-if="type=='bars'" width="300" height="300" viewBox="0 0 140 200">
        <rect x="0" y="160" rx="0" ry="0" width="25" height="40" />
        <rect x="30" y="145" rx="0" ry="0" width="25" height="55" />
        <rect x="60" y="126" rx="0" ry="0" width="25" height="74" />
        <rect x="90" y="80" rx="0" ry="0" width="25" height="120" />
        <rect x="120" y="142" rx="0" ry="0" width="25" height="58" />
    </content-loader>

  <content-loader
    v-if="type == 'article'"
    width="100%"
    height="350"
    viewBox="0 0 700 350"
    :primaryColor="primaryColor" :secondaryColor="secondaryColor"
  >
    <rect x="4" y="8" rx="3" ry="3" width="8" height="317" />
    <rect x="7" y="317" rx="3" ry="3" width="669" height="8" />
    <rect x="669" y="9" rx="3" ry="3" width="7" height="313" />
    <rect x="5" y="8" rx="3" ry="3" width="669" height="7" />
    <rect x="114" y="52" rx="6" ry="6" width="483" height="15" />
    <circle cx="77" cy="60" r="15" />
    <rect x="116" y="105" rx="6" ry="6" width="420" height="15" />
    <circle cx="78" cy="113" r="15" />
    <rect x="115" y="158" rx="6" ry="6" width="483" height="15" />
    <circle cx="78" cy="166" r="15" />
    <rect x="117" y="211" rx="6" ry="6" width="444" height="15" />
    <circle cx="79" cy="219" r="15" />
    <rect x="117" y="263" rx="6" ry="6" width="483" height="15" />
    <circle cx="80" cy="271" r="15" />
 </content-loader>

</template>

<script>
import { ContentLoader } from 'vue-content-loader'

export default {
  props: {
      type: String,
      primaryColor: {
        default: "#eee"
      },
      secondaryColor: {
        default: "#ddd"
      },
  },
  components: {
    ContentLoader
  }
}
</script>