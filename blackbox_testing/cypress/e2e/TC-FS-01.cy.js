describe("TC-FS-01", () => {
    it("tests TC-FS-01", () => {
        cy.login();
    cy.viewport(1089, 825);
    cy.visit("http://localhost:8080/apps/files/files");
    cy.get("tr:nth-of-type(4) > td.files-list__row-actions > div > button path").click();
    cy.get("#share-input-qaj0g").click();
    cy.get("#share-input-qaj0g").type("abdulhaseeb.39393@gmail.com");
    cy.get("span.option__lineone > strong").click();
    cy.get("#zahov").click();
    cy.get("#app-sidebar-vue button.button-vue--vue-primary > span > span").click();
  });
});
